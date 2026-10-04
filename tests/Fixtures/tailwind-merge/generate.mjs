// Regenerates the fixtures that pin this port to the JS tailwind-merge it
// ports, extended with the grammar additions ported from shadcn-ui/cn (see
// GrammarAdditionsTest):
//
// - class-map.json: the default class map flattened the way ClassMapCompiler
//   flattens it, plus the conflict tables (UpstreamClassMapTest).
// - corpus/*.json: real class lists harvested from open source codebases
//   (cn's bench corpora), each with tailwind-merge's output (CorpusParityTest).
//
// Not part of the test run. To regenerate, after bumping the tracked version:
//
//   npm --prefix /tmp/tw-merge install tailwind-merge@3.7.0
//   TAILWIND_MERGE_PREFIX=/tmp/tw-merge node tests/Fixtures/tailwind-merge/generate.mjs
import { readFileSync, writeFileSync } from "node:fs"
import { createRequire } from "node:module"

// Pinned so the corpora only change when this script does.
const CN_COMMIT = "b7ec0fce43d824a60517fab508584eb860fdf717"
const REPOSITORIES = ["shadcn-ui", "calcom", "dub", "supabase"]

const prefix = process.env.TAILWIND_MERGE_PREFIX
if (!prefix) throw new Error("set TAILWIND_MERGE_PREFIX to a directory where tailwind-merge is installed")

const require = createRequire(prefix.replace(/\/?$/, "/"))
const { extendTailwindMerge, fromTheme, getDefaultConfig, mergeConfigs, validators } = require("tailwind-merge")
const { version } = JSON.parse(readFileSync(`${prefix}/node_modules/tailwind-merge/package.json`, "utf8"))

// Mirrors cn's packages/conformance/tests/reference.mjs.
const additions = {
  extend: {
    classGroups: {
      contain: [{ contain: ["none", "content", "strict", validators.isArbitraryVariable, validators.isArbitraryValue] }],
      "contain-size": [{ contain: ["size", "inline-size"] }],
      "contain-layout": ["contain-layout"],
      "contain-paint": ["contain-paint"],
      "contain-style": ["contain-style"],
      "bg-image": [{ "bg-gradient-to": ["t", "tr", "r", "br", "b", "bl", "l", "tl"] }],
      "auto-cols": [{ "auto-cols": [fromTheme("spacing")] }],
      "auto-rows": [{ "auto-rows": [fromTheme("spacing")] }],
    },
    conflictingClassGroups: {
      contain: ["contain-size", "contain-layout", "contain-paint", "contain-style"],
      "contain-size": ["contain"],
      "contain-layout": ["contain"],
      "contain-paint": ["contain"],
      "contain-style": ["contain"],
    },
  },
}
const twMerge = extendTailwindMerge(additions)
const config = mergeConfigs(getDefaultConfig(), additions)

const write = (path, json) => writeFileSync(new URL(path, import.meta.url), json + "\n")

// Same walk as ClassMap + ClassMapCompiler: literals keyed by path, validator
// lists keyed by dash-terminated path ('' for the root), validators by name.
const validatorNames = new Map(Object.entries(validators).map(([name, validator]) => [validator, name]))
const newNode = () => ({ next: new Map(), validators: [], classGroupId: null })
const root = newNode()
const getPart = (node, path) => {
  for (const part of path.split("-")) {
    if (!node.next.has(part)) node.next.set(part, newNode())
    node = node.next.get(part)
  }
  return node
}
const processDefinition = (definition, node, classGroupId) => {
  if (typeof definition === "string") {
    ;(definition === "" ? node : getPart(node, definition)).classGroupId = classGroupId
  } else if (typeof definition === "function" && definition.isThemeGetter) {
    for (const inner of definition(config.theme)) processDefinition(inner, node, classGroupId)
  } else if (typeof definition === "function") {
    const name = validatorNames.get(definition)
    if (!name) throw new Error(`unknown validator in class group ${classGroupId}`)
    node.validators.push([name, classGroupId])
  } else {
    for (const [key, inner] of Object.entries(definition)) {
      for (const innerDefinition of inner) processDefinition(innerDefinition, getPart(node, key), classGroupId)
    }
  }
}
for (const [classGroupId, definitions] of Object.entries(config.classGroups)) {
  for (const definition of definitions) processDefinition(definition, root, classGroupId)
}
const literals = {}
const validatorLists = {}
if (root.validators.length > 0) validatorLists[""] = root.validators
const collect = (node, path) => {
  if (node.classGroupId !== null) literals[path] = node.classGroupId
  if (node.validators.length > 0) validatorLists[path + "-"] = node.validators
  for (const [part, child] of node.next) collect(child, path + "-" + part)
}
for (const [part, child] of root.next) collect(child, part)

write(
  "class-map.json",
  JSON.stringify(
    {
      version,
      literals,
      validators: validatorLists,
      conflictingClassGroups: config.conflictingClassGroups,
      conflictingClassGroupModifiers: config.conflictingClassGroupModifiers,
      orderSensitiveModifiers: config.orderSensitiveModifiers,
      postfixLookupClassGroups: config.postfixLookupClassGroups ?? [],
    },
    null,
    2
  )
)
console.log(`class-map.json: ${Object.keys(literals).length} literals, ${Object.keys(validatorLists).length} validator nodes (tailwind-merge ${version})`)

for (const repository of REPOSITORIES) {
  const url = `https://raw.githubusercontent.com/shadcn-ui/cn/${CN_COMMIT}/packages/conformance/bench/corpora/${repository}.json`
  const response = await fetch(url)
  if (!response.ok) throw new Error(`${url}: ${response.status}`)

  const inputs = new Set()
  for (const call of await response.json()) {
    const input = call.filter((argument) => typeof argument === "string" && argument !== "").join(" ")
    if (input.trim() !== "") inputs.add(input)
  }

  const cases = [...inputs].map((input) => [input, twMerge(input)])
  write(`corpus/${repository}.json`, JSON.stringify(cases).replaceAll('"],["', '"],\n["'))
  console.log(`corpus/${repository}.json: ${cases.length} cases`)
}

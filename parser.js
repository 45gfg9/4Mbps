'use strict'

const docTree = JSON.parse(document.getElementById('doc-tree').textContent)

const create = {
    // create element with classes
    element(tag, ...classes) {
        const e = document.createElement(tag)
        e.classList.add(...classes)
        return e
    },

    // create a div-input block for translation unit
    divInputBlock(e) {
        const div = create.element('div', 'tr-unit')
        // the original quote
        const p = create.element('p')
        p.append(e.value)
        div.append(p)

        // assert: e.ignore === undefined

        // create text input div
        const input = create.element('div', 'div-input')
        // a better still-bad approach
        input.contentEditable = 'plaintext-only'
        div.append(input)

        return div
    },

    // create a BBCode-tag indicator div
    divTag(tag) {
        const tagDiv = create.element('div', 'tag')
        tagDiv.append(tag)
        return tagDiv
    },

    // create an attribute input block
    // value: fills into input
    attributeBlock(value, key) {
        const div = create.element('div', 'attribute')

        const input = create.element('input', 'attr-input')
        const label = create.element('label')
        input.value = value

        if (key !== undefined) {
            const u = create.element('u')
            u.append(key)
            label.append(u)
            label.append(': ')
        }

        div.append(label)
        label.append(input)

        return div
    }
}

const collect = {
    all() {
        const ret = this.entry(rootDiv)
        console.log(ret)

        const result = document.getElementById('result-bbcode')
        result.hidden = false
        result.innerText = ret
    },

    trUnit(unit) {
        const p = unit.firstChild

        return ''
    },

    // recursively collect
    entry(unit) {
        let ret = ''
        for (const entry of unit.childNodes) {
            if ('tr-unit' in entry.classList) {
                ret += this.trUnit(entry)
            } else if ('entry' in entry.classList) {
                ret += this.entry(entry)
            }
        }

        return ret
    },
}

Node.prototype.apply4Mbps = function (body) {
    // for every body entry
    for (const e of body) {
        // handle this entry.
        this.appendChild(buildNode(e))
    }
    return this
}

// Build HTML node from entry.
function buildNode(e) {
    // this root node.
    const entryBlock = create.element('div', 'entry')

    // extract tag and body
    const body = e.body
    let tag = e.tag

    // delete tag and body, leaving extra keys to process
    delete e.body
    delete e.tag

    // handle tags
    if (tag === null) {
        // text
        if (e['ignore'] === undefined) {
            // this is a translation unit
            return create.divInputBlock(e)
        }
        // else, this is an attribute
        return create.attributeBlock(e.value)
    } else if (tag === 'header') {
        // this is a header node
        tag = 'h' + e['level']
        delete e['level']
    }

    // append tag indicator
    entryBlock.append(create.divTag(tag))

    // for every other attributes
    for (const tag in e) {
        // display them
        entryBlock.append(create.attributeBlock(e[tag], tag))
    }

    // if this node has body
    body && entryBlock.apply4Mbps(body)

    return entryBlock
}

const rootDiv = document.getElementById('root-div')
rootDiv.apply4Mbps(docTree.body)

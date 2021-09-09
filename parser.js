'use strict'

const docTree = JSON.parse(document.getElementById('doc-tree').textContent)
const docBody = []
for (let obj of docTree.body) {
    docBody.push(obj, { tag: null, value: '\n', ignore: true })
}
docTree.body = docBody

const create = {
    element(tag, ...classes) {
        const e = document.createElement(tag)
        e.classList.add(...classes)
        return e
    },

    text(text) {
        return document.createTextNode(text)
    },

    divInputBlock(e) {
        const div = create.element('div', 'tr-unit')
        const p = create.element('p')
        p.appendChild(create.text(e.value))
        div.appendChild(p)

        if (e['ignore'] === undefined) {
            const input = create.element('div', 'div-input')
            input.contentEditable = true
            div.appendChild(input)
        }

        return div
    },

    divTag(tag) {
        const tagDiv = create.element('div', 'tag')
        tagDiv.appendChild(create.text(tag))
        return tagDiv
    },

    labelInputBlock(key) {
        const div = create.element('div', 'attribute')

        const label = create.element('label')
        const u = create.element('u')
        u.appendChild(create.text(key))
        label.appendChild(u)
        label.appendChild(create.text(': '))

        const input = create.element('input', 'attr-input')
        input.contentEditable = true

        div.appendChild(label)
        div.appendChild(input)

        return div
    }
}

const collect = {
    all() {

    }
}

const rootDiv = create.element('div')
rootDiv.id = 'root-div'

Node.prototype.apply4Mbps = function (body) {
    for (const e of body) {
        this.appendChild(construct(e))
    }
    return this
}

// what the f is this.
function construct(e) {
    const entryBlock = create.element('div', 'entry')
    const body = e.body
    let tag = e.tag

    delete e.body
    delete e.tag

    if (tag === null) {
        return create.divInputBlock(e)
    } else if (tag === 'header') {
        tag = 'h' + e['level']
        delete e['level']
    }

    entryBlock.setAttribute('data-tag', tag)

    entryBlock.appendChild(create.divTag(tag))

    for (const tag in e) {
        entryBlock.appendChild(create.labelInputBlock(tag))
    }

    body && entryBlock.apply4Mbps(body)

    return entryBlock
}

document.getElementById('editor')
    .appendChild(rootDiv.apply4Mbps(docTree.body))

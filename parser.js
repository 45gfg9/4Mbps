'use strict';

const docTree = JSON.parse(document.getElementById('doc-tree').textContent)

function crE(tag, ...classes) {
    const e = document.createElement(tag)
    e.classList.add(...classes)
    return e
}

function crT(text) {
    return document.createTextNode(text)
}

function createDivInputBlock(e) {
    const div = crE('div', 'tr-unit')
    const p = crE('p')
    p.appendChild(crT(e.value))
    div.appendChild(p)

    if (e['ignore'] === undefined) {
        const input = crE('div', 'div-input')
        input.contentEditable = true
        div.appendChild(input)
    }

    return div
}

function createLabelInputBlock(key) {
    const div = crE('div', 'attribute')

    const label = crE('label')
    const u = crE('u')
    u.appendChild(crT(key))
    label.appendChild(u)
    label.appendChild(crT(': '))

    const input = crE('input', 'attr-input')
    input.contentEditable = true

    div.appendChild(label)
    div.appendChild(input)

    return div
}

function createTagDiv(tag) {
    const tagDiv = crE('div', 'tag')
    tagDiv.appendChild(crT(tag))
    return tagDiv
}

function collectAll() {

}

const rootDiv = crE('div')
rootDiv.id = 'root-div'

Node.prototype.apply4Mbps = function (body) {
    for (const e of body) {
        this.appendChild(construct(e))
    }
    return this
}

// what the f is this.
function construct(e) {
    const entryBlock = crE('div', 'entry')
    const body = e.body
    let tag = e.tag

    delete e.body
    delete e.tag

    if (tag === null) {
        return createDivInputBlock(e)
    } else if (tag === 'header') {
        tag = 'h' + e['level']
        delete e['level']
    }

    entryBlock.setAttribute('data-tag', tag)

    entryBlock.appendChild(createTagDiv(tag))

    for (const tag in e) {
        entryBlock.appendChild(createLabelInputBlock(tag))
    }

    body && entryBlock.apply4Mbps(body)

    return entryBlock
}

document.getElementById('editor')
    .appendChild(rootDiv.apply4Mbps(docTree.body))

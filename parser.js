'use strict';

const docTree = JSON.parse(document.getElementById('doc-tree').textContent)

const pp = {
    crE(tag, ...classes) {
        const e = document.createElement(tag)
        e.classList.add(...classes)
        return e
    },

    crT(text) {
        return document.createTextNode(text)
    },

    createDivInputBlock(e) {
        const div = pp.crE('div', 'tr-unit')
        const p = pp.crE('p')
        p.appendChild(pp.crT(e.value))
        div.appendChild(p)

        if (e['ignore'] === undefined) {
            const input = pp.crE('div', 'div-input')
            input.contentEditable = true
            div.appendChild(input)
        }

        return div
    },

    createTagDiv(tag) {
        const tagDiv = pp.crE('div', 'tag')
        tagDiv.appendChild(pp.crT(tag))
        return tagDiv
    },

    createLabelInputBlock(key) {
        const div = pp.crE('div', 'attribute')

        const label = pp.crE('label')
        const u = pp.crE('u')
        u.appendChild(pp.crT(key))
        label.appendChild(u)
        label.appendChild(pp.crT(': '))

        const input = pp.crE('input', 'attr-input')
        input.contentEditable = true

        div.appendChild(label)
        div.appendChild(input)

        return div
    }
}

const collect = {
    collect() {
        // TODO
    }
}

const rootDiv = pp.crE('div')
rootDiv.id = 'root-div'

Node.prototype.apply4Mbps = function (body) {
    for (const e of body) {
        this.appendChild(construct(e))
    }
    return this
}

// what the f is this.
function construct(e) {
    const entryBlock = pp.crE('div', 'entry')
    const body = e.body
    let tag = e.tag

    delete e.body
    delete e.tag

    if (tag === null) {
        return pp.createDivInputBlock(e)
    } else if (tag === 'header') {
        tag = 'h' + e['level']
        delete e['level']
    }

    entryBlock.setAttribute('data-tag', tag)

    entryBlock.appendChild(pp.createTagDiv(tag))

    for (const tag in e) {
        entryBlock.appendChild(pp.createLabelInputBlock(tag))
    }

    body && entryBlock.apply4Mbps(body)

    return entryBlock
}

document.getElementById('editor')
    .appendChild(rootDiv.apply4Mbps(docTree.body))

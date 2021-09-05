const tree = JSON.parse(document.getElementById('tree').textContent)

function crE() { return document.createElement(...arguments) }

function crT() { return document.createTextNode(...arguments) }

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
    const div = crE('div')
    div.classList.add('entry-block')

    let tag = e.tag
    delete e.tag

    if (tag === null) {
        const wordDiv = crE('div')
        wordDiv.appendChild(crT(e.value))
        if (e.ignore !== undefined) {
            wordDiv.classList.add('word-block')
            wordDiv.appendChild(crE('input'))
        }
        return wordDiv
    }

    const body = e.body
    delete e.body

    if (tag === 'header') {
        tag = 'h' + e.level
        delete e.level
    }

    const tagDiv = crE('div')
    tagDiv.textContent = tag
    tagDiv.classList.add('tag')
    div.appendChild(tagDiv)

    for (const key in e) {
        const label = crE('label')
        const input = crE('input')
        label.appendChild(crT(key + ": "))
        label.appendChild(input)
        div.appendChild(label)
    }

    return div.apply4Mbps(body)
}

document.getElementById('editor')
    .appendChild(rootDiv.apply4Mbps(tree.body))

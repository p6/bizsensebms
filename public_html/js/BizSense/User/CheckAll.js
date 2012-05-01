function checkByDiv(checked) {
    dojo.query("input").forEach(function(node, index, arr) { node.checked= checked;} )
}


function mvdb_wp_metabox_new_list(e, target) {
    e.preventDefault();

    var section = target;
    // Find current section
    while (
        section &&
        section.nodeName &&
        section.nodeName != 'SECTION' &&
        section.parentElement
    ) {
        section = section.parentElement;
    }

    if (section === target) return;

    var lists = section.querySelector('ol');

    if (!lists) return;

    var list = lists.querySelector('li');

    if (!list) return;

    var newList = list.cloneNode(true);

    var inputs = newList.querySelectorAll('input');
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].value = null;
    }

    var textareas = newList.querySelectorAll('textarea');
    for (var i = 0; i < textareas.length; i++) {
        textareas[i].value = null;
    }

    lists.append(newList);
}

function mvdb_wp_metabox_delete_list(e, target) {
    e.preventDefault();

    var list = target;
    // Find current section
    while (
        list &&
        list.nodeName &&
        list.nodeName != 'LI' &&
        list.parentElement
    ) {
        list = list.parentElement;
    }

    if (!list) return;

    if (confirm(target.getAttribute('data-delete-confirm'))) {
        mvdb_wp_metabox_delete_list_confirm(list);
    }
}

function mvdb_wp_metabox_delete_list_confirm(list) {
    list.parentElement.removeChild(list);
}
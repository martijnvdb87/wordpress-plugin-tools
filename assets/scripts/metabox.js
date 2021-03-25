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

    var list = section.querySelector('template').content;

    if (!list) return;

    var newList = list.cloneNode(true);

    var inputs = newList.querySelectorAll('input, textarea');
    for (var i = 0; i < inputs.length; i++) {
        inputs[i].setAttribute('value', '');
        inputs[i].value = null;

        mvdb_wp_metabox_new_lists_input_id(newList, inputs[i]);
    }

    lists.append(newList);

    mvdb_wp_metabox_reindex_inputs(lists);
}

function mvdb_wp_metabox_new_lists_input_id(list, input) {
    var id = input.id;
    var parts = id.split('-');
    parts.pop();

    var chars = '0123456789abcdef';
    var random = '';
    while (random.length < 23) {
        var num = Math.floor(Math.random() * Math.floor(chars.length - 1));
        random += chars[num];
    }

    parts.push(random);
    var newId = parts.join('-');

    var label = list.querySelector('label[for="' + id + '"]');

    if (label) {
        label.setAttribute('for', newId);
    }

    input.id = newId;
}

function mvdb_wp_metabox_order(e, target) {
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

    var container = list.parentElement;

    var radioChecked = []
    var inputs = container.querySelectorAll('input[type="radio"]');
    inputs.forEach(function(input) {
        if (input.checked) {
            radioChecked.push(input);
        }
    });

    if (target.getAttribute('data-order') == 'higher') {
        if (list.previousElementSibling) {
            container.insertBefore(list, list.previousElementSibling);
        }
    } else {
        if (list.nextElementSibling) {
            container.insertBefore(list.nextElementSibling, list);
        }
    }

    setTimeout(function() {
        radioChecked.forEach(function(input) {
            input.checked = true;
        });
    });

    var parent = list.parentElement;
    mvdb_wp_metabox_reindex_inputs(parent);
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
    var parent = list.parentElement;
    parent.removeChild(list);

    mvdb_wp_metabox_reindex_inputs(parent);
}

function mvdb_wp_metabox_reindex_inputs(container) {
    var lists = container.querySelectorAll('li');

    lists.forEach(function(list, index) {
        var inputs = list.querySelectorAll('input, textarea');
        for (var i = 0; i < inputs.length; i++) {

            var name = inputs[i].name;
            name = name.replace(/\[(\d+?)?\]/, '[' + index + ']');

            inputs[i].name = name;
        }
    });
}
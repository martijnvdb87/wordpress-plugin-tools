(function() {
    document.addEventListener('click', function(e) {
        var classActions = {
            'martijnvdb-wordpress-plugin-tools-metabox-new-list': newList,
            'martijnvdb-wordpress-plugin-tools-metabox-order': orderLists,
            'martijnvdb-wordpress-plugin-tools-metabox-section-deletion': deleteList,
        }

        var classes = Object.keys(classActions);
        var target = e.target;

        do {
            for (var i = 0; i < classes.length; i++) {
                if (target.matches('.' + classes[i])) {
                    return classActions[classes[i]](e, target);
                }
            }

            target = target.parentElement;
        }
        while (target);
    });


    function newList(e, target) {
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
            if (!(inputs[i].type == 'radio' || inputs[i].type == 'checkbox')) {
                inputs[i].setAttribute('value', '');
                inputs[i].value = null;
            }

            newListInputId(newList, inputs[i]);
        }

        var radioChecked = []
        var inputs = lists.querySelectorAll('input[type="radio"]');
        inputs.forEach(function(input) {
            if (input.checked) {
                radioChecked.push(input);
            }
        });

        lists.append(newList);

        radioChecked.forEach(function(input) {
            input.checked = true;
        });

        reindexInputs(lists);
    }

    function newListInputId(list, input) {
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

    function orderLists(e, target) {
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
        reindexInputs(parent);
    }

    function deleteList(e, target) {
        e.preventDefault();
        var target = e.target;

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
            deleteListConfirm(list);
        }
    }

    function deleteListConfirm(list) {
        var parent = list.parentElement;
        parent.removeChild(list);

        reindexInputs(parent);
    }

    function reindexInputs(container) {
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
})();
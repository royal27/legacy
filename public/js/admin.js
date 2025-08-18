// Main JavaScript file for the admin panel.
document.addEventListener('DOMContentLoaded', function() {
    handleFooterLinks();
    handleMenuEditor();
    handleMenuCopy();
});

function handleMenuCopy() {
    const adminContent = document.querySelector('.admin-main-content');
    if (!adminContent) return;

    adminContent.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('copy-link-btn')) {
            const linkToCopy = e.target.dataset.link;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(linkToCopy).then(() => {
                    const originalText = e.target.textContent;
                    e.target.textContent = 'Copied!';
                    setTimeout(() => {
                        e.target.textContent = originalText;
                    }, 1500);
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            }
        }
    });
}

// Logic for dynamic footer links in settings page
function handleFooterLinks() {
    const linksContainer = document.getElementById('footer-links-container');
    const addLinkButton = document.getElementById('add-footer-link');

    if (!addLinkButton || !linksContainer) return;

    addLinkButton.addEventListener('click', function() {
        const index = linksContainer.querySelectorAll('.footer-link-item').length;
        const newItem = document.createElement('div');
        newItem.className = 'footer-link-item';
        newItem.style.display = 'flex';
        newItem.style.gap = '10px';
        newItem.style.marginBottom = '10px';
        newItem.innerHTML = `
            <input type="text" name="footer_links[${index}][text]" placeholder="Link Text" class="form-control" required>
            <input type="text" name="footer_links[${index}][url]" placeholder="Link URL" class="form-control" required>
            <button type="button" class="btn btn-danger remove-link-btn">Remove</button>
        `;
        linksContainer.appendChild(newItem);
    });

    linksContainer.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-link-btn')) {
            e.target.closest('.footer-link-item').remove();
        }
    });
}

// Logic for the menu manager page
function handleMenuEditor() {
    const form = document.getElementById('menu-item-form');
    if (!form) return;

    const editButtons = document.querySelectorAll('.edit-menu-btn');
    const clearButton = document.getElementById('clear-menu-form');
    const formTitle = document.getElementById('menu-form-title');

    const idField = document.getElementById('menu-id');
    const nameField = document.getElementById('menu-name');
    const linkField = document.getElementById('menu-link');
    const typeField = document.getElementById('menu-type');
    const parentField = document.getElementById('menu-parent-id');
    const orderField = document.getElementById('menu-display-order');
    const permsField = document.getElementById('menu-permissions');

    const resetForm = () => {
        form.reset();
        idField.value = '0';
        formTitle.textContent = 'Add New Menu Item';
        // Deselect all options in the multi-select
        for (let option of permsField.options) {
            option.selected = false;
        }
    };

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const dataset = this.dataset;
            idField.value = dataset.id;
            nameField.value = dataset.name;
            linkField.value = dataset.link;
            typeField.value = dataset.menu_type;
            parentField.value = dataset.parent_id;
            orderField.value = dataset.display_order;

            const permissions = JSON.parse(dataset.permissions);
            for (let option of permsField.options) {
                option.selected = permissions.includes(parseInt(option.value));
            }

            formTitle.textContent = `Edit Menu Item: "${dataset.name}"`;
            window.scrollTo({ top: form.offsetTop - 20, behavior: 'smooth' });
        });
    });

    clearButton.addEventListener('click', resetForm);
}

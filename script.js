document.addEventListener('DOMContentLoaded', function () {
    const menuItems = document.querySelectorAll('.menu > li');

    menuItems.forEach(item => {
        item.addEventListener('mouseover', function () {
            this.querySelector('.submenu').style.display = 'block';
        });

        item.addEventListener('mouseout', function () {
            this.querySelector('.submenu').style.display = 'none';
        });
    });
});


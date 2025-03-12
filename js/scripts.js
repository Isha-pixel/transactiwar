document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll("form");

    forms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            const inputs = form.querySelectorAll("input[required]");
            let valid = true;

            inputs.forEach(function (input) {
                if (input.value.trim() === "") {
                    alert(input.name + " is required.");
                    valid = false;
                }
            });

            if (!valid) event.preventDefault();
        });
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll("form");

    forms.forEach((form) => {
        form.addEventListener("submit", (event) => {
            const inputs = form.querySelectorAll("input[required]");
            let valid = true;

            inputs.forEach((input) => {
                if (input.value.trim() === "") {
                    alert(`${input.name} is required.`);
                    valid = false;
                }
            });

            if (!valid) event.preventDefault();
        });
    });
});

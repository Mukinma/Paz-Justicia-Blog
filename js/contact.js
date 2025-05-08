document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("contactForm");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const name = form.name.value.trim();
        const lastname = form.lastname.value.trim();
        const email = form.email.value.trim();
        const message = form.message.value.trim();

        if (!name || !lastname || !email || !message) {
            alert("Please fill out all fields.");
            return;
        }

        alert("Thank you! Your message has been sent. 💌");
        form.reset();
    });
});

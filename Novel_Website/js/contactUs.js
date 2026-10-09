    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let name = document.getElementById('name').value.trim();
        let email = document.getElementById('email').value.trim();
        let subject = document.getElementById('subject').value.trim();
        let message = document.getElementById('message').value.trim();
        let formMessage = document.getElementById('formMessage');

        if (!name || !email || !subject || !message) {
            alert('All fields are required!');
            return;
        }

        if (!validateEmail(email)) {
            alert('Please enter a valid email address!');
            return;
        }

        formMessage.style.display = 'block';
        setTimeout(() => {
            formMessage.style.display = 'none';
        }, 3000);

        // Clear form fields after submission
        document.getElementById('contactForm').reset();
    });

    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
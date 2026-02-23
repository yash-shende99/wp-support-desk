fetch('../../components/default/index.html')
        .then(response => response.text()) // Corrected arrow function syntax
        .then(data => {
            document.getElementsByClassName('default')[0].innerHTML = data; // Access the first element with the class 'default'
        })
        .catch(error => console.error('Error loading content:', error)); // Add error handling
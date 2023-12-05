/ Function to validate login form
function validateLoginForm() {
    var username = document.forms["loginForm"]["loginUser"].value;
    var password = document.forms["loginForm"]["loginPass"].value;

    if (username === "" || password === "") {
        alert("Username and Password must be filled out");
        return false;
    }
    return true;
}
// Function to validate registration form
function validateRegisterForm() {
    var username = document.forms["registerForm"]["username"].value;
    var password = document.forms["registerForm"]["password"].value;
    var confirmPassword = document.forms["registerForm"]["confirmPassword"].value;

    if (username === "" || password === "" || confirmPassword === "") {
        alert("All fields must be filled out");
        return false;
    }
    if (password !== confirmPassword) {
        alert("Passwords do not match");
        return false;
    }
    return true;
}

// Function to validate create post form
function validateCreatePostForm() {
    var caption = document.forms["createPostForm"]["caption"].value;
    var image = document.forms["createPostForm"]["image[]"].value;

    if (caption === "" && image === "") {
        alert("Please provide a caption or an image.");
        return false;
    }
    return true;
}

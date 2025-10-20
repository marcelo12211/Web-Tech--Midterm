// auth.js — localStorage version
document.addEventListener("DOMContentLoaded", () => {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");

  // LOGIN
  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const msg = document.getElementById("loginMessage");
      msg.textContent = "";

      const email = loginForm.email.value.trim();
      const password = loginForm.password.value.trim();

      const users = JSON.parse(localStorage.getItem("rms_users") || "[]");
      const user = users.find((u) => u.email === email && u.password === password);

      if (user) {
        localStorage.setItem("rms_user", JSON.stringify({ name: user.name, email: user.email }));
        window.location.href = "dashboard.html";
      } else {
        msg.textContent = "Invalid credentials. Please try again or register.";
      }
    });
  }

  // REGISTER
  if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const msg = document.getElementById("registerMessage");
      msg.textContent = "";

      const name = registerForm.name.value.trim();
      const email = registerForm.email.value.trim();
      const password = registerForm.password.value.trim();

      if (!name || !email || !password) {
        msg.textContent = "All fields are required.";
        return;
      }

      const users = JSON.parse(localStorage.getItem("rms_users") || "[]");
      if (users.find((u) => u.email === email)) {
        msg.textContent = "Email already exists.";
        return;
      }

      users.push({ name, email, password });
      localStorage.setItem("rms_users", JSON.stringify(users));

      msg.style.color = "green";
      msg.textContent = "Registered successfully. Redirecting to login...";
      setTimeout(() => (window.location.href = "index.html"), 1000);
    });
  }
});

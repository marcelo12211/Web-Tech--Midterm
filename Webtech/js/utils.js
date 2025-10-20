// utils.js — helper functions for auth and redirects
function requireLogin(){
  const user = localStorage.getItem("rms_user");
  if(!user){ window.location.href = "index.html"; return null; }
  try{ return JSON.parse(user); } catch(e){ window.location.href = "index.html"; return null; }
}

function logout(){
  localStorage.removeItem("rms_user");
  window.location.href = "index.html";
}

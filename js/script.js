const mobileNav = document.querySelector(".hamburger");
const navbar = document.querySelector(".menubar");

const toggleNav = () => {
  navbar.classList.toggle("active");
  mobileNav.classList.toggle("hamburger-active");
};
mobileNav.addEventListener("click", () => toggleNav());

// INICIO navbar schrink scrool //
// When the user scrolls down 80px from the top of the document, resize the navbar's padding and the logo's font size
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 80 || document.documentElement.scrollTop > 80) {
    document.getElementById("navbar").style.padding = "0px 0px";
    document.getElementById("logo").style.fontSize = "20px";
    document.querySelectorAll("#nav .menulinks li a").forEach(function(link) {
      link.style.fontSize = "20px";
    });
  } else {
    document.getElementById("navbar").style.padding = "20px 10px";
    document.getElementById("logo").style.fontSize = "40px";
    // document.getElementById("navbar").style.boxShadow = "none"; // Remove a sombra do navbar
    document.querySelectorAll("#navbar .menulinks li a").forEach(function(link) {
      link.style.fontSize = "50px";
    });
  }
}

// FIM navbar schrink scrool //


// INICIO -- Receber respectivo ano e inserir no footer //

document.addEventListener('DOMContentLoaded', function() {
  var currentYear = new Date().getFullYear();
  document.getElementById('currentYear').textContent = currentYear;
 });

 // FIM -- Receber respectivo ano e inserir no footer //

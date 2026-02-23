
let search = document.querySelectorAll(".search");
let searchInput = document.querySelector(".search-input");
let searchIcon2 = document.querySelector(".search-icon");
let searchContainers = document.querySelectorAll(".search-container");
search.forEach((search, index) => {
  search.addEventListener("click", () => {
    let searchContainer = searchContainers[index];
    if (
      searchContainer.style.display === "none" ||
      searchContainer.style.display === ""
    ) {
      searchContainer.style.display = "block";
    } else {
      searchContainer.style.display = "none";
    }

    searchInput.classList.add("input-animation");
    searchIcon2.classList.add("search-icon-animation");
  });
});

// script.js
document.addEventListener("DOMContentLoaded", function () {
  // const searchInput = document.querySelector('.search-input');
  const overlay = document.querySelector(".overlay");

  // Show overlay when search input is focused
  searchInput.addEventListener("focus", function () {
    overlay.style.display = "block";
  });

  // Hide overlay when clicking outside of the search input
  document.addEventListener("click", function (event) {
    if (
      !searchInput.contains(event.target) &&
      !overlay.contains(event.target)
    ) {
      overlay.style.display = "none";
    }
  });

  // Also hide overlay when the user clicks inside the overlay
  overlay.addEventListener("click", function () {
    overlay.style.display = "none";
  });
});

console.log("let's start javascript");

async function getSongs() {
  let a = await fetch("http://127.0.0.1:5500/songs/");
  let response = await a.text();
  let div = document.createElement("div");
  div.innerHTML = response;
  let as = div.getElementsByTagName("a");

  let songs = [];
  for (let i = 0; i < as.length; i++) {
    const element = as[i];
    if (element.href.endsWith(".mp3")) {
      songs.push(element.href);
    }
  }

  return songs;
}

async function main() {
  let songs = await getSongs();
  console.log(songs);
}

main();

document.addEventListener("DOMContentLoaded", () => {
  const playlistsContainer = document.querySelector(".playlists");

  // Data for playlists and cards
  const playlists = [
    {
      title: "99 Playlists",
      cards: [
        {
          title: "A mix of the biggest pop, dance, and hip hop party songs",
          imageUrl: "../public/partyhits.jpg",
        },
        {
          title: "New music from Central Cee, Gucci Mane and more.",
          imageUrl: "../public/rapcaviar.jpg",
        },
        {
          title: "Kick back to the best new and recent chill hits.",
          imageUrl: "../public/chillhits.jpg",
        },
        {
          title: "chill beats, lofi vibes, new tracks every week...",
          imageUrl: "../public/lofibeats.jpg",
        },
      ],
    },
    {
      title: "Bollywood Masala",
      cards: [
        {
          title: "Hottest Hindi music served here. Cover - Bad Newz",
          imageUrl: "../public/hot hits.jpg",
        },
        {
          title:
            "Every track you're listening/should be listening to ;) Cover- Stree 2",
          imageUrl: "../public/trending.jpg",
        },
        {
          title:
            "Let these songs be the background score to your love story. Cover - Bad Newz",
          imageUrl: "../public/bollywood mush.jpg",
        },
        {
          title:
            "Bollywood songs that ruled hearts in the Y2K decade. Cover - Jab We Met",
          imageUrl: "../public/allout.jpg",
        },
      ],
    },
    {
      title: "Popular Albums",
      cards: [
        { title: "Aashiqui 2", imageUrl: "../public/aashiqui2.jpg" },
        { title: "Sidhu Moosewala", imageUrl: "../public/moosewala.jpg" },
        { title: "Animal", imageUrl: "../public/animal.jpg" },
        { title: "Sajni (From Lapata Ladies)", imageUrl: "../public/sajni.jpg" },
      ],
    },
    {
      title: "Pop Favorites",
      cards: [
        { title: "Dance Party", imageUrl: "../public/danceparty.jpg" },
        { title: "Aavesham (Original Musictrack)", imageUrl: "../public/Aavesham.jpg" },
        { title: "Ek tha Raja", imageUrl: "../public/king.jpg" },
        {
          title: "Bollywood crooner's essential songs.",
          imageUrl: "../public/thisis.jpg",
        },
      ],
    },
  ];

  // Function to create a card element
  function createCard(title, imageUrl) {
    const card = document.createElement("div");
    card.className = "card";
    card.innerHTML = `
            <div class="play">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="45px" height="45px">
                    <defs>
                        <radialGradient id="grad1" cx="50%" cy="50%" r="50%" fx="50%" fy="50%">
                            <stop offset="0%" style="stop-color: #ffffff; stop-opacity: 1" />
                            <stop offset="70%" style="stop-color: #e0e0e0; stop-opacity: 1" />
                            <stop offset="100%" style="stop-color: #a0a0a0; stop-opacity: 1" />
                        </radialGradient>
                        <linearGradient id="shine" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" style="stop-color: rgba(255, 255, 255, 0.6); stop-opacity: 1" />
                            <stop offset="100%" style="stop-color: rgba(255, 255, 255, 0); stop-opacity: 0" />
                        </linearGradient>
                        <filter id="shadow" x="-50%" y="-50%" width="200%" height="200%">
                            <feOffset result="offOut" in="SourceAlpha" dx="3" dy="3" />
                            <feGaussianBlur result="blurOut" in="offOut" stdDeviation="3" />
                            <feBlend in="SourceGraphic" in2="blurOut" mode="normal" />
                        </filter>
                    </defs>
                    <circle cx="50" cy="50" r="50" fill="url(#grad1)" />
                    <circle cx="50" cy="50" r="50" fill="url(#shine)" />
                    <polygon points="35,25 75,50 35,75" fill="black" filter="url(#shadow)" />
                </svg>
            </div>
            <img src="${imageUrl}" alt="">
            <p>${title}</p>
        `;
    return card;
  }

  // Function to create a playlist section
  function createPlaylist(title, cards) {
    const playlistSection = document.createElement("div");
    playlistSection.className = "playlist";
    playlistSection.innerHTML = `
            <h1>${title}</h1>
            <div class="cardContainer">
                 ${cards
                   .map(
                     (card) =>
                       `<div class="card">${
                         createCard(card.title, card.imageUrl).innerHTML
                       }</div>`
                   )
                   .join("")}
            </div>
        `;
    return playlistSection;
  }

  // Populate playlists
  playlists.forEach((playlist) => {
    const playlistElement = createPlaylist(playlist.title, playlist.cards);
    playlistsContainer.appendChild(playlistElement);
  });
});

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
let loginBtn = document.querySelector(".loginBtn");

let loginPage = document.querySelector(".login-page");

let loginClose = document.querySelector(".login-close");

// Show the login page when the login button is clicked
loginBtn.addEventListener("click", function() {
    loginPage.style.display = 'block';
    pre.style.transform = "rotateX(0deg) rotateY(0deg)";
});

// Hide the login page when the close button is clicked
loginClose.addEventListener("click", function() {
    loginPage.style.display = 'none';

});


// LOGIN PAGe
// Select the element you want to rotate
const pre = document.querySelector(".login-content"); // Adjust the selector to match your element

// Add the mousemove event listener
document.addEventListener("mousemove", (e) => {
  rotateElement(e, pre);
});

let isRotating = true; // Flag to track if rotation is active

// Function to calculate and apply rotation
function rotateElement(event, element) {
    if (!isRotating) return; // If not rotating, exit the function
    const x = event.clientX;
    const y = event.clientY;

    // Find the center of the window
    const middleX = window.innerWidth / 2;
    const middleY = window.innerHeight / 2;

    // Calculate the offset from the center
    const offsetX = ((x - middleX) / middleX) * 45;
    const offsetY = ((y - middleY) / middleY) * 45;

    // Apply rotation
    element.style.transform = `rotateX(${-1 * offsetY}deg) rotateY(${offsetX}deg)`;
}


document.addEventListener("click", () => {
  isRotating = !isRotating; // Toggle the rotation state
});

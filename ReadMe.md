FILESYSTEM STRUCTURE

music-web-app/
│
├── client/                     # Frontend files
│   ├── public/                 # Static assets (images, fonts, etc.)
│   ├── src/                    # Source code
│   │   ├── assets/             # Fonts, icons, other static files
│   │   ├── components/         # Reusable UI components (e.g., navbar, buttons)
│   │   │   ├── default/        #component of main body normal structure
│   │   │   │   ├── index.html  
│   │   │   │   ├── script.js            
│   │   │   │   ├── style.css            
│   │   │   │   └── utility.css            
│   │   ├── pages/              # Pages (e.g., login, playlists, home)
│   │   │   ├── playlist1/      # Playlist
│   │   │   │   ├── index.html  
│   │   │   │   ├── script.js            
│   │   │   │   ├── style.css            
│   │   │   │   └── utility.css  
│   │   ├── services/           # Frontend services (API calls, etc.)
│   │   ├── styles/             # CSS/SCSS files
│   │   ├── index.html          # Main HTML page
│   │   └── script.js             # Main JavaScript entry point
│   └── package.json            # Frontend dependencies
│
├── server/                     # Backend files
│   ├── config/                 # Configuration files (DB, environment variables)
│   ├── controllers/            # Route controllers (business logic)
│   ├── middleware/             # Custom middleware (e.g., authentication)
│   ├── models/                 # Mongoose models (user, playlist, song)
│   ├── routes/                 # API routes (auth, playlist, song)
│   ├── uploads/                #songs file
│   ├── utils/                  # Utility functions/helpers (e.g., error handling)
│   ├── app.js                  # Main Express application setup
│   └── package.json            # Backend dependencies
│
├── .env                        # Environment variables (API keys, DB connection strings)
├── README.md                   # Project documentation
└── package.json                # Project dependencies (root-level, managing both frontend/backend)






http://localhost:5000/api/songs  # for getting song in thunderclient
<!-- update 5723 -->

<!-- update 6631 -->

<!-- update 4337 -->

<!-- update 2002 -->

<!-- update 5543 -->

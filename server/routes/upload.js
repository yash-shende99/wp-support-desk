const express = require("express");
const multer = require("multer");
const path = require("path");
const Song = require("../models/song");

const router = express.Router();

const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, "../uploads/"); 
  },
  filename: (req, file, cb) => {
    cb(null, Date.now() + path.extname(file.originalname)); 
  },
});

const upload = multer({ storage });

router.post("/upload", upload.single("song"), async (req, res) => {
  if (!req.file) {
    return res.status(400).json({ message: "No file uploaded" });
  }
  try {
    const song = new Song({
      title: req.body.title,
      artist: req.body.artist,
      album: req.body.album,
      year: req.body.year,
      genre: req.body.genre,
      duration: req.body.duration,
      filePath: req.file.path,
    });
    // const savedSong = await song.save();
    // res.status(201).json(savedSong);
    // Comment this out temporarily to test file upload
    // const savedSong = await song.save();
    res
      .status(201)
      .json({ message: "File uploaded successfully", file: req.file });
  } catch (error) {
    console.error("Error saving song:", error);
    res
      .status(500)
      .json({ message: "Internal server error", error: error.message });
  }
});

router.get("/songs", async (req, res) => {
  try {
    const songs = await Song.find();
    res.status(200).json(songs);
  } catch (error) {
    res.status(400).json({ message: error.message });
  }
});

module.exports = router;

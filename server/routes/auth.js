
const express = require('express');
const router = express.Router();
const User = require('../models/user');

// Register route
router.post('/register', async (req, res) => {
   const { username, password } = req.body;

   // Create new user
   try {
      const newUser = new User({ username, password });
      await newUser.save();
      res.status(201).json({ message: 'User registered successfully' });
   } catch (error) {
      res.status(500).json({ error: 'Registration failed' });
   }
});

module.exports = router;

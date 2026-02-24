const mongoose = require("mongoose");
require('dotenv').config();

const connectDb = async () => {
    try {
        await mongoose.connect(process.env.MONGO_URL, {
            // useNewUrlParser: true,
            // useUnifiedTopology: true,
            ssl: true, 
        });
        console.log('MongoDB connected successfully.');
    } catch (error) {
        console.error('MongoDB connection failed:', error);
        process.exit(1);
    }
};

module.exports = connectDb;

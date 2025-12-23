CREATE DATABASE library;
USE library;

CREATE TABLE Admin (
    Admin_ID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL
);

INSERT INTO Admin (Username, Password) VALUES ('admin', 'admin123');

CREATE TABLE Publisher (
    Pub_ID INT AUTO_INCREMENT PRIMARY KEY,
    Pub_Name VARCHAR(100)
);

CREATE TABLE Books (
    Book_ID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(100),
    Author VARCHAR(100),
    Price DECIMAL(10,2),
    Available BOOLEAN DEFAULT 1,
    Pub_ID INT,
    FOREIGN KEY (Pub_ID) REFERENCES Publisher(Pub_ID)
);

CREATE TABLE Member (
    Member_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100),
    Address VARCHAR(255)
);

CREATE TABLE Issued_Books (
    Issue_ID INT AUTO_INCREMENT PRIMARY KEY,
    Book_ID INT,
    Member_ID INT,
    Issue_Date DATE,
    Return_Date DATE,
    FOREIGN KEY (Book_ID) REFERENCES Books(Book_ID),
    FOREIGN KEY (Member_ID) REFERENCES Member(Member_ID)
);





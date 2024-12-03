CREATE DATABASE library;
USE library;

-- Table: account
CREATE TABLE account (
    accountID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(70) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    confirmPassword VARCHAR(255) NOT NULL,
    accountType VARCHAR(20) CHECK (accountType IN ('user', 'librarian'))
);
ALTER TABLE account DROP COLUMN confirmPassword;

-- Table: user
CREATE TABLE user (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(70) UNIQUE NOT NULL,
    unpaidFines INT DEFAULT 0,
    FOREIGN KEY (email) REFERENCES account(email) ON DELETE CASCADE ON UPDATE CASCADE
);
ALTER TABLE user
ADD COLUMN Status INT DEFAULT 1;
ALTER TABLE issuedBooks
ADD COLUMN fineAmount DECIMAL(10, 2) DEFAULT 0;

ALTER TABLE issuedBooks
ADD COLUMN finePaid DECIMAL(10, 2) DEFAULT 0;


-- Table: librarian
CREATE TABLE librarian (
    librarianID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    email VARCHAR(70) UNIQUE NOT NULL,
    FOREIGN KEY (email) REFERENCES account(email) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: book
CREATE TABLE book (
    ISBN VARCHAR(15) PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    yearOfPublication INT,
    totalCopies INT NOT NULL,
    noOfCopiesOnShelf INT NOT NULL,
    authors VARCHAR(200),
    category VARCHAR(20),
    image VARCHAR(1000),
    bookName VARCHAR(100),           -- New column
    CatId INT,                       -- Foreign key for category (if applicable)
    AuthorId INT,                    -- Foreign key for author (if applicable)
    bookPrice DECIMAL(10, 2),        -- Price of the book
    isIssued INT DEFAULT 0,          -- Status indicating if the book is issued (1 = issued, 0 = available)
    RegDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,   -- Registration date of the book
    UpdationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP   -- Last updated date
);

-- Table: review
CREATE TABLE review (
    reviewID INT AUTO_INCREMENT PRIMARY KEY,
    reviewText VARCHAR(500),
    userID INT NOT NULL,
    ISBN VARCHAR(15) NOT NULL,
    FOREIGN KEY (userID) REFERENCES user(userID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (ISBN) REFERENCES book(ISBN) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: shelf
CREATE TABLE shelf (
    shelfID INT AUTO_INCREMENT PRIMARY KEY,
    capacity INT NOT NULL
);

-- Table: bookCopies
CREATE TABLE bookCopies (
    copyID INT AUTO_INCREMENT PRIMARY KEY,
    ISBN VARCHAR(15) NOT NULL,
    bookStatus VARCHAR(20) NOT NULL,
    dueDate DATE,
    shelfID INT NOT NULL,
    FOREIGN KEY (ISBN) REFERENCES book(ISBN) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (shelfID) REFERENCES shelf(shelfID) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: rating
CREATE TABLE rating (
    ratingID INT AUTO_INCREMENT PRIMARY KEY,
    ISBN VARCHAR(15) NOT NULL,
    userID INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    FOREIGN KEY (ISBN) REFERENCES book(ISBN) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (userID) REFERENCES user(userID) ON DELETE CASCADE ON UPDATE CASCADE
);
ALTER TABLE bookcopies DROP FOREIGN KEY bookcopies_ibfk_1;
ALTER TABLE bookcopies ADD CONSTRAINT bookcopies_ibfk_1 FOREIGN KEY (ISBN) REFERENCES book (ISBN) ON DELETE CASCADE ON UPDATE CASCADE;

-- Table: issuedBooks
CREATE TABLE issuedBooks (
    issueID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    copyID INT NOT NULL,
    issueDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    returnDate TIMESTAMP NULL,
    FOREIGN KEY (userID) REFERENCES user(userID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (copyID) REFERENCES bookCopies(copyID) ON DELETE CASCADE ON UPDATE CASCADE
);
-- Insert data into account table
INSERT INTO account (username, email, password, confirmPassword, accountType) 
VALUES
    ('admin', 'admin@gmail.com', 'f925916e2754e5e03f75dd58a5733251', 'f925916e2754e5e03f75dd58a5733251', 'librarian'),
    ('rajeev', 'rajeev@gmail.com', 'b808a5a228865001e791e1a6e11897b6', 'b808a5a228865001e791e1a6e11897b6', 'user'),
    ('pranitha', 'pranitha@gmail.com', '60a52b20019768aebcc58adfbdca6849', '60a52b20019768aebcc58adfbdca6849', 'user'),
    ('sohan', 'sohan@gmail.com', 'c6b4ec62fc78ac15c998ab5332e7272f', 'c6b4ec62fc78ac15c998ab5332e7272f', 'user'),
    ('sanjay', 'sanjay@gmail.com', '89a606a2a403066606aa0f5e7dad9134', '89a606a2a403066606aa0f5e7dad9134', 'user'),
    ('user1', 'user1@gmail.com', '8a13a81b63c9f02d897e8b39dd21372f', '8a13a81b63c9f02d897e8b39dd21372f', 'user'),
    ('user2', 'user2@gmail.com', '415ae01d78998c8191a416ddd8cabe33', '415ae01d78998c8191a416ddd8cabe33', 'user'),
    ('user3', 'user3@gmail.com', '456ab20472cd48e1e621f3e8ac0f3eb1', '456ab20472cd48e1e621f3e8ac0f3eb1', 'user'),
    ('user4', 'user4@gmail.com', '55ad0d3b9bbf07c562872c30d3d5f57b', '55ad0d3b9bbf07c562872c30d3d5f57b', 'user'),
    ('user5', 'user5@gmail.com', '54acad74cb89d6d08e3a71941d83f030', '54acad74cb89d6d08e3a71941d83f030', 'user');

-- Insert data into user table
INSERT INTO user (name, email, unpaidFines)
SELECT a.username, a.email, 0 
FROM account a
WHERE a.accountType = 'user'
AND NOT EXISTS (SELECT 1 FROM user u WHERE u.email = a.email);

-- Insert into librarian table
INSERT INTO librarian (name, email)
SELECT a.username, a.email
FROM account a
WHERE a.accountType = 'librarian'
AND NOT EXISTS (SELECT 1 FROM librarian l WHERE l.email = a.email);
ALTER TABLE bookCopies MODIFY copyID VARCHAR(255);

ALTER TABLE issuedBooks DROP FOREIGN KEY FK_CopyID;
ALTER TABLE bookCopies MODIFY copyID VARCHAR(255);
ALTER TABLE issuedBooks MODIFY copyID VARCHAR(255);

-- Insert data into book table
INSERT INTO book (ISBN, title, yearOfPublication, totalCopies, noOfCopiesOnShelf, authors, category, image, CatId, AuthorId, isIssued, RegDate, UpdationDate, description)
VALUES
    ('222333', 'PHP And MySql programming', 2024, 10, 10, 'Anuj Kumar', 'Technology', '1efecc0ca822e40b7b673c0d79ae943f.jpg', 1, 1, 0, NOW(), NOW(), 'A book on PHP and MySQL programming for beginners.'),
    ('1111', 'Physics', 2024, 5, 5, 'HC Verma', 'Science', 'dd8267b57e0e4feee5911cb1e1a03a79.jpg', 2, 2, 0, NOW(), NOW(), 'A classic Physics book for students.'),
    ('9350237695', 'Murach\'s MySQL', 2024, 15, 15, 'Anuj Kumar', 'Technology', '5939d64655b4d2ae443830d73abc35b6.jpg', 1, 1, 0, NOW(), NOW(), 'A comprehensive guide to MySQL.'),
    ('B019MO3WCM', 'WordPress for Beginners 2022: A Visual Step-by-Step Guide to Mastering WordPress', 2022, 20, 20, 'Dr. Andy Williams', 'Technology', '144ab706ba1cb9f6c23fd6ae9c0502b3.jpg', 1, 3, 0, NOW(), NOW(), 'A beginner-friendly guide to mastering WordPress.'),
    ('B09NKWH7NP', 'WordPress Mastery Guide:', 2022, 10, 10, 'Kyle Hill', 'Technology', '90083a56014186e88ffca10286172e64.jpg', 1, 4, 0, NOW(), NOW(), 'Advanced tips for mastering WordPress.'),
    ('B07C7M8SX9', 'Rich Dad Poor Dad: What the Rich Teach Their Kids About Money That the Poor and Middle Class Do Not', 2020, 25, 25, 'Robert T. Kiyosak', 'General', '52411b2bd2a6b2e0df3eb10943a5b640.jpg', 3, 5, 0, NOW(), NOW(), 'A personal finance classic.'),
    ('1848126476', 'The Girl Who Drank the Moon', 2018, 10, 10, 'Kelly Barnhill', 'General', 'f05cd198ac9335245e1fdffa793207a7.jpg', 3, 6, 0, NOW(), NOW(), 'A fantasy novel with magical elements.'),
    ('007053246X', 'C++: The Complete Reference, 4th Edition', 2016, 5, 5, 'Herbert Schildt', 'Programming', '36af5de9012bf8c804e499dc3c3b33a5.jpg', 4, 7, 0, NOW(), NOW(), 'Comprehensive guide to C++ programming.'),
    ('GBSJ36344563', 'ASP.NET Core 5 for Beginners', 2021, 8, 8, 'Herbert Schildt', 'Programming', 'b1b6788016bbfab12cfd2722604badc9.jpg', 4, 7, 0, NOW(), NOW(), 'A beginner’s guide to ASP.NET Core 5.');

-- Insert data into shelf table
INSERT INTO shelf (capacity) 
VALUES 
    (50), 
    (100), 
    (75);
INSERT INTO shelf (shelfID, capacity) VALUES
(4, 200),  -- Shelf 4 with a capacity of 200 books
(5, 180);  -- Shelf 5 with a capacity of 180 books


-- Insert data into bookCopies table
UPDATE bookcopies
SET shelfID = 1
WHERE ISBN = '007053246X';

UPDATE bookcopies
SET shelfID = 1
WHERE ISBN = '007053246X';

UPDATE bookcopies
SET shelfID = 2
WHERE ISBN = '1111';

UPDATE bookcopies
SET shelfID = 2
WHERE ISBN = '1111';

UPDATE bookcopies
SET shelfID = 3
WHERE ISBN = '1848126476';

UPDATE bookcopies
SET shelfID = 3
WHERE ISBN = '1848126476';

UPDATE bookcopies
SET shelfID = 4
WHERE ISBN = '222333';

UPDATE bookcopies
SET shelfID = 4
WHERE ISBN = '222333';

UPDATE bookcopies
SET shelfID = 5
WHERE ISBN = '9350237695';

UPDATE bookcopies
SET shelfID = 5
WHERE ISBN = '9350237695';

-- Continue adding more as necessary for the other copies...


-- Insert reviews into review table (using valid userID from 1 to 8)
INSERT INTO review (reviewText, userID, ISBN)
VALUES
    ('Great book on PHP and MySQL', 1, '222333'),
    ('Excellent physics book for beginners', 2, '1111'),
    ('Best MySQL tutorial I have ever read', 3, '9350237695'),
    ('Easy to follow WordPress guide', 4, 'B019MO3WCM'),
    ('In-depth WordPress mastery guide', 5, 'B09NKWH7NP'),
    ('Amazing finance book', 6, 'B07C7M8SX9'),
    ('Beautifully written fantasy novel', 7, '1848126476'),
    ('Perfect C++ reference', 8, '007053246X');

-- Insert ratings into rating table (using valid userID from 1 to 8)
INSERT INTO rating (ISBN, userID, rating)
VALUES
    ('222333', 1, 5),
    ('1111', 2, 4),
    ('9350237695', 3, 5),
    ('B019MO3WCM', 4, 3),
    ('B09NKWH7NP', 5, 4),
    ('B07C7M8SX9', 6, 5),
    ('1848126476', 7, 4),
    ('007053246X', 8, 5);

-- Insert data into issuedBooks table
-- Inserting records into issuedBooks
INSERT INTO issuedBooks (userID, copyID, issueDate) VALUES
(1, '007053246X_1', '2024-11-01'),
(2, '007053246X_2', '2024-11-02'),
(3, '007053246X_3', '2024-11-03'),
(4, '007053246X_4', '2024-11-04'),
(5, '007053246X_5', '2024-11-05'),
(6, '1111_1', '2024-11-06'),
(7, '1111_2', '2024-11-07'),
(8, '1111_3', '2024-11-08'),
(9, '1848126476_1', '2024-11-09');
-- Add more entries as required...
;

    
UPDATE account
SET password = 'aaa49836b3c025adc4d540578a3d6cbb'
WHERE username = 'rajeev';

ALTER TABLE issuedBooks ADD COLUMN ISBN VARCHAR(20), ADD FOREIGN KEY (ISBN) REFERENCES book(ISBN);
UPDATE issuedBooks ib
JOIN bookCopies bc ON ib.copyID = bc.copyID
SET ib.ISBN = bc.ISBN;
-- Add ISBN column to issuedBooks table

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `AuthorName` varchar(159) DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblauthors`
--

INSERT INTO `authors` (`id`, `AuthorName`, `creationDate`, `UpdationDate`) VALUES
(1, 'Anuj kumar', '2024-01-25 07:23:03', '2024-02-04 06:34:19'),
(2, 'Chetan Bhagatt', '2024-01-25 07:23:03', '2024-02-04 06:34:26'),
(3, 'Anita Desai', '2024-01-25 07:23:03', '2024-02-04 06:34:26'),
(4, 'HC Verma', '2024-01-25 07:23:03', '2024-02-04 06:34:26'),
(5, 'R.D. Sharma ', '2024-01-25 07:23:03', '2024-02-04 06:34:26'),
(9, 'fwdfrwer', '2024-01-25 07:23:03', '2024-02-04 06:34:26'),
(10, 'Dr. Andy Williams', '2024-01-25 07:23:03', '2024-02-04 06:34:26'),
(11, 'Kyle Hill', '2024-01-25 07:23:03', '2024-02-04 06:34:26'),
(12, 'Robert T. Kiyosak', '2024-01-25 07:23:03', '2024-02-04 06:34:26'),
(13, 'Kelly Barnhill', '2024-01-25 07:23:03', '2024-02-04 06:34:26'),
(14, 'Herbert Schildt', '2024-01-25 07:23:03', '2024-02-04 06:34:26');
CREATE TABLE `category` (
   `id` int(11) NOT NULL,
   `CategoryName` varchar(150) DEFAULT NULL,
   `Status` int(1) DEFAULT NULL,
   `CreationDate` timestamp NULL DEFAULT current_timestamp(),
   `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
--
-- Dumping data for table `tblcategory`
--
ALTER TABLE bookCopies MODIFY shelfID INT NULL;

INSERT INTO `category` (`id`, `CategoryName`, `Status`, `CreationDate`, `UpdationDate`) VALUES
(4, 'Romantic', 1, '2024-01-31 07:23:03', '2024-02-04 06:33:43'),
(5, 'Technology', 1, '2024-01-31 07:23:03', '2024-02-04 06:33:51'),
(6, 'Science', 1, '2024-01-31 07:23:03', '2024-02-04 06:33:51'),
(7, 'Management', 1, '2024-01-31 07:23:03', '2024-02-04 06:33:51'),
(8, 'General', 1, '2024-01-31 07:23:03', '2024-02-04 06:33:51'),
(9, 'Programming', 1, '2024-01-31 07:23:03', '2024-02-04 06:33:51');
ALTER TABLE category MODIFY COLUMN categoryName VARCHAR(150) NOT NULL;
ALTER TABLE category MODIFY COLUMN Status INT NOT NULL DEFAULT 1; -- assuming 1 means active
ALTER TABLE category MODIFY COLUMN CreationDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE category MODIFY COLUMN UpdationDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE book ADD description TEXT;
-- Update Category IDs
UPDATE book
SET CatId = (SELECT id FROM category WHERE categoryName = 'Programming')
WHERE category = 'Programming';

UPDATE book
SET CatId = (SELECT id FROM category WHERE categoryName = 'Science')
WHERE category = 'Science';

UPDATE book
SET CatId = (SELECT id FROM category WHERE categoryName = 'General')
WHERE category = 'General';

UPDATE book
SET CatId = (SELECT id FROM category WHERE categoryName = 'Technology')
WHERE category = 'Technology';

-- If you have any other categories, add similar update queries for them.

-- Update Author IDs
UPDATE book
SET AuthorId = (SELECT id FROM authors WHERE AuthorName = 'Herbert Schildt')
WHERE authors = 'Herbert Schildt';

UPDATE book
SET AuthorId = (SELECT id FROM authors WHERE AuthorName = 'HC Verma')
WHERE authors = 'HC Verma';

UPDATE book
SET AuthorId = (SELECT id FROM authors WHERE AuthorName = 'Kelly Barnhill')
WHERE authors = 'Kelly Barnhill';

UPDATE book
SET AuthorId = (SELECT id FROM authors WHERE AuthorName = 'Anuj kumar')
WHERE authors = 'Anuj kumar';

UPDATE book
SET AuthorId = (SELECT id FROM authors WHERE AuthorName = 'Dr. Andy Williams')
WHERE authors = 'Dr. Andy Williams';

UPDATE book
SET AuthorId = (SELECT id FROM authors WHERE AuthorName = 'Robert T. Kiyosak')
WHERE authors = 'Robert T. Kiyosak';

UPDATE book
SET AuthorId = (SELECT id FROM authors WHERE AuthorName = 'Kyle Hill')
WHERE authors = 'Kyle Hill';

-- If you have any other authors, add similar update queries for them.
SET SQL_SAFE_UPDATES = 0;
ALTER TABLE book
DROP COLUMN bookPrice, 
DROP COLUMN bookName;
ALTER TABLE bookcopies
DROP COLUMN dueDate;
ALTER TABLE issuedbooks
ADD COLUMN dueDate DATE;
UPDATE issuedbooks
SET dueDate = DATE_ADD(issueDate, INTERVAL 30 DAY);
ALTER TABLE issuedBooks
ADD CONSTRAINT FK_CopyID FOREIGN KEY (copyID) REFERENCES bookCopies(copyID),
ADD CONSTRAINT FK_UserID FOREIGN KEY (userID) REFERENCES user(userID);
DELIMITER $$

CREATE TRIGGER after_book_insert
AFTER INSERT ON book
FOR EACH ROW
BEGIN
    DECLARE i INT DEFAULT 1;
    
    -- Loop through and insert the number of copies specified in totalCopies
    WHILE i <= NEW.totalCopies DO
        INSERT INTO bookCopies (ISBN, copyID, bookStatus, shelfID)
        VALUES (NEW.ISBN, CONCAT(NEW.ISBN, '_', i), 0, NULL);  -- Assuming bookStatus = 0 (available), shelfID can be NULL initially
        SET i = i + 1;
    END WHILE;
END $$

DELIMITER ;
DELIMITER $$


DELIMITER ;
ALTER TABLE bookCopies
MODIFY COLUMN copyID INT AUTO_INCREMENT;

INSERT INTO book (ISBN, title, yearOfPublication, totalCopies, noOfCopiesOnShelf, authors, category, image, CatId, AuthorId, isIssued, RegDate, UpdationDate, description) 
VALUES 
('9781119235552', 'Java Programming for Beginners', 5, 15, 15, 'Kyle Hill', 'Technology', 'e5f9a4f2b2d447fbbd6fc1239876abcd.jpg', 1, 1, 0, '2024-02-01 08:10:00', '2024-02-05 09:00:00', 'A beginner\'s guide to Java programming with practical examples.'),
('0136042597', 'Artificial Intelligence: A Modern Approach', 9, 16, 16, 'Stuart Russell, Peter Norvig', 'Technology', 'c2d93f5e94b24578aa8e123dbbd3efab.jpg', 1, 2, 0, '2024-02-01 08:15:00', '2024-02-05 09:10:00', 'Comprehensive coverage of AI algorithms, methods, and systems.'),
('1492043422', 'Data Science for Business', 8, 17, 17, 'Foster Provost, Tom Fawcett', 'Business', '34af34d68c8d4b13ab4e8bde2e9ad123.jpg', 2, 3, 0, '2024-02-01 08:20:00', '2024-02-05 09:20:00', 'Essential guide for data science applications in business contexts.'),
('1593279280', 'Python Crash Course', 5, 18, 18, 'Eric Matthes', 'Programming', 'af8d3c4d6b4a4187a8e13f78b45d0f12.jpg', 3, 4, 0, '2024-02-01 08:25:00', '2024-02-05 09:30:00', 'A hands-on introduction to Python programming for beginners.'),
('1617294438', 'Deep Learning with Python', 5, 19, 19, 'Francois Chollet', 'Technology', '5bc3a8e4f0d44612aabbb998aa123ce4.jpg', 1, 5, 1, '2024-02-01 08:30:00', '2024-02-05 09:40:00', 'A practical guide to deep learning with Python and Keras.'),
('0132350882', 'Clean Code: A Handbook of Agile Software Craftsmanship', 5, 20, 20, 'Robert C. Martin', 'Programming', '89d7efc4a68b43e8bbd123fab9a71f01.jpg', 3, 6, 0, '2024-02-01 08:35:00', '2024-02-05 09:50:00', 'A guide to writing clean, maintainable code in Java and other languages.'),
('020161622X', 'The Pragmatic Programmer', 5, 21, 21, 'Andrew Hunt, David Thomas', 'Programming', '2d7e0fa4a69b4c12bbd45e89fab0aa23.jpg', 3, 7, 0, '2024-02-01 08:40:00', '2024-02-05 10:00:00', 'Practical advice for software developers from two experienced professionals.'),
('0262033844', 'Introduction to Algorithms', 6, 22, 22, 'Thomas H. Cormen, Charles E. Leiserson', 'Computer Science', 'b23d5e4c71ab469bb7891f01ced56a34.jpg', 4, 8, 0, '2024-02-01 08:45:00', '2024-02-05 10:10:00', 'An authoritative guide to algorithms in computer science, widely used in education.'),
('9780999241165', 'Machine Learning Yearning', 7, 23, 23, 'Andrew Ng', 'Technology', 'e89b35e47a29bc8eabbdd109ab1ce289.jpg', 1, 9, 0, '2024-02-01 08:50:00', '2024-02-05 10:20:00', 'Practical insights into the design of machine learning systems, by a renowned expert.'),
('1593277571', 'You Don’t Know JS', 5, 24, 24, 'Kyle Simpson', 'Programming', '11d57ec4e69bbdd124f890c12e3adf11.jpg', 3, 10, 0, '2024-02-01 08:55:00', '2024-02-05 10:30:00', 'In-depth exploration of JavaScript for serious programmers.'),
('0131103628', 'The C Programming Language', 5, 25, 25, 'Brian W. Kernighan, Dennis M. Ritchie', 'Programming', 'c7bb4a45df128bc124e09aa7def3af45.png', 3, 11, 0, '2024-02-01 09:00:00', '2024-02-05 10:40:00', 'Classic text on the C programming language, written by its creators.'),
('0134685997', 'Effective Java', 6, 26, 26, 'Joshua Bloch', 'Programming', '889d4a67fb8a4c21a8d123fabc045d12.jpg', 3, 12, 0, '2024-02-01 09:05:00', '2024-02-05 10:50:00', 'Best practices for writing robust, maintainable Java code, updated for Java 8.');
INSERT INTO bookCopies (ISBN, bookStatus, dueDate, shelfID)
VALUES 
('9781119235552', 'Available', '2024-03-01', 1),
('0136042597', 'Available', '2024-03-02', 1),
('1492043422', 'Available', '2024-03-03', 2),
('1593279280', 'Available', '2024-03-04', 2),
('1617294438', 'Available', '2024-03-05', 3),
('0132350882', 'Available', '2024-03-06', 3),
('020161622X', 'Available', '2024-03-07', 4),
('0262033844', 'Available', '2024-03-08', 4),
('9780999241165', 'Available', '2024-03-09', 5),
('1593277571', 'Available', '2024-03-10', 5),
('0131103628', 'Available', '2024-03-11', 6),
('0134685997', 'Available', '2024-03-12', 6);
DELIMITER $$

CREATE TRIGGER after_book_insert
AFTER INSERT ON book
FOR EACH ROW
BEGIN
    DECLARE i INT DEFAULT 1;
    
    -- Loop through the totalCopies for the new book and insert individual book copies
    WHILE i <= NEW.totalCopies DO
        INSERT INTO bookCopies (ISBN, copyID, bookStatus, shelfID)
        VALUES (NEW.ISBN, CONCAT(NEW.ISBN, '-', i), 'Available', 1);  -- Assuming shelfID is 1
        SET i = i + 1;
    END WHILE;
END$$

-- Change delimiter
DELIMITER //

-- Create the trigger
CREATE TRIGGER before_book_update
BEFORE UPDATE ON book
FOR EACH ROW
BEGIN
    -- Check if the updated noOfCopiesOnShelf exceeds totalCopies
    IF NEW.noOfCopiesOnShelf > NEW.totalCopies THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'noOfCopiesOnShelf cannot exceed totalCopies';
    END IF;
END //

-- Change delimiter back to semicolon
DELIMITER ;
SELECT * FROM book WHERE ISBN = '222333';
UPDATE book 
SET noOfCopiesOnShelf = 15 
WHERE ISBN = '222333';
SELECT * FROM book WHERE ISBN = '222333';
UPDATE book 
SET noOfCopiesOnShelf = 8 
WHERE ISBN = '222333';

CREATE TABLE payments (
    paymentID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT,
    issueID INT,
    amount DECIMAL(10, 2),
    paymentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paymentStatus VARCHAR(50) DEFAULT 'Completed',
    FOREIGN KEY (userID) REFERENCES user(userID),
    FOREIGN KEY (issueID) REFERENCES issuedBooks(issueID)
);
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES user(userID)
);

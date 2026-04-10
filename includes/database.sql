SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `tbladmin` (
  `ID` int(10) NOT NULL,
  `AdminName` char(50) DEFAULT NULL,
  `UserName` char(50) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `AdminRegdate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`ID`, `AdminName`, `UserName`, `MobileNumber`, `Email`, `Password`, `AdminRegdate`) VALUES
(1, 'Admin', 'admin', 7898799798, 'tester1@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2019-07-25 06:21:50');

CREATE TABLE `tblappointment` (
  `ID` int(10) NOT NULL,
  `AptNumber` varchar(80) DEFAULT NULL,
  `Name` varchar(120) DEFAULT NULL,
  `Email` varchar(120) DEFAULT NULL,
  `PhoneNumber` bigint(11) DEFAULT NULL,
  `AptDate` varchar(120) DEFAULT NULL,
  `AptTime` varchar(120) DEFAULT NULL,
  `Services` varchar(120) DEFAULT NULL,
  `ApplyDate` timestamp NULL DEFAULT current_timestamp(),
  `Remark` varchar(250) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL,
  `RemarkDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Insert sample appointments with various statuses and dates

INSERT INTO `tblappointment` (`ID`, `AptNumber`, `Name`, `Email`, `PhoneNumber`, `AptDate`, `AptTime`, `Services`, `ApplyDate`, `Remark`, `Status`, `RemarkDate`) VALUES
(1, 'APT001', 'John Doe', 'john@gmail.com', 9876543210, '2025-10-26', '10:00 AM', 'Hair Cut', '2025-10-20 08:30:00', NULL, 'Confirmed', NULL),
(2, 'APT002', 'Jane Smith', 'jane@gmail.com', 9876543211, '2025-10-26', '11:00 AM', 'O3 Facial', '2025-10-21 09:15:00', NULL, 'Confirmed', NULL),
(3, 'APT003', 'Mike Johnson', 'mike@gmail.com', 9876543212, '2025-10-26', '02:00 PM', 'Beard Trim', '2025-10-22 10:20:00', 'Customer requested cancellation', 'Cancelled', '2025-10-25 14:30:00'),
(4, 'APT004', 'Sarah Williams', 'sarah@gmail.com', 9876543213, '2025-10-26', '03:00 PM', 'Deluxe Pedicure', '2025-10-23 11:45:00', NULL, 'Confirmed', NULL),
(5, 'APT005', 'David Brown', 'david@gmail.com', 9876543214, '2025-10-25', '09:00 AM', 'Style Haircut', '2025-10-19 08:00:00', 'Completed successfully', 'Completed', '2025-10-25 10:30:00'),
(6, 'APT006', 'Emily Davis', 'emily@gmail.com', 9876543215, '2025-10-26', '04:00 PM', 'Body Spa', '2025-10-24 12:30:00', NULL, 'Confirmed', NULL),
(7, 'APT007', 'Robert Wilson', 'robert@gmail.com', 9876543216, '2025-10-24', '10:00 AM', 'Hair Color', '2025-10-18 07:45:00', 'Completed successfully', 'Completed', '2025-10-24 11:45:00'),
(8, 'APT008', 'Linda Martinez', 'linda@gmail.com', 9876543217, '2025-10-26', '05:00 PM', 'Normal Menicure', '2025-10-25 13:20:00', NULL, 'Confirmed', NULL),
(9, 'APT009', 'James Anderson', 'james@gmail.com', 9876543218, '2025-10-23', '11:00 AM', 'Charcol Facial', '2025-10-17 09:30:00', 'Completed successfully', 'Completed', '2025-10-23 12:30:00'),
(10, 'APT010', 'Patricia Taylor', 'patricia@gmail.com', 9876543219, '2025-10-26', '01:00 PM', 'Fruit Facial', '2025-10-25 15:10:00', 'No show', 'Cancelled', '2025-10-26 13:15:00'),
(11, 'APT011', 'Michael Thomas', 'michael@gmail.com', 9876543220, '2025-10-22', '03:00 PM', 'Hair Wash', '2025-10-16 10:00:00', 'Completed successfully', 'Completed', '2025-10-22 16:00:00'),
(12, 'APT012', 'Jennifer Jackson', 'jennifer@gmail.com', 9876543221, '2025-10-27', '10:00 AM', 'Deluxe Menicure', '2025-10-25 16:45:00', NULL, 'Pending', NULL),
(13, 'APT013', 'Christopher White', 'chris@gmail.com', 9876543222, '2025-10-21', '02:00 PM', 'MUSTACHE TRIM', '2025-10-15 08:20:00', 'Completed successfully', 'Completed', '2025-10-21 14:45:00'),
(14, 'APT014', 'Jessica Harris', 'jessica@gmail.com', 9876543223, '2025-10-26', '12:00 PM', 'Normal Pedicure', '2025-10-25 17:30:00', NULL, 'Confirmed', NULL),
(15, 'APT015', 'Daniel Clark', 'daniel@gmail.com', 9876543224, '2025-10-20', '04:00 PM', 'Beard Trim', '2025-10-14 11:15:00', 'Completed successfully', 'Completed', '2025-10-20 16:30:00');

CREATE TABLE `tblcustomers` (
  `ID` int(10) NOT NULL,
  `Name` varchar(120) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `MobileNumber` bigint(11) DEFAULT NULL,
  `Gender` enum('Female','Male','Transgender') DEFAULT NULL,
  `Details` mediumtext DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


INSERT INTO `tblcustomers` (`ID`, `Name`, `Email`, `MobileNumber`, `Gender`, `Details`, `CreationDate`, `UpdationDate`) VALUES
(2, 'Rahul Singh', 'singh@gmail.com', 5565565656, 'Male', 'Taken haircut by him', '2023-12-08 11:10:02', '2023-12-11 04:15:02'),
(5, 'Test user', 'testuser@gmail.com', 1234567890, 'Female', 'Test', '2023-12-08 11:10:02', '2023-12-11 04:15:10'),
(6, 'Manish', 'manish@gmail.com', 9879879798, 'Male', 'vjhgjhghg;lk;lklnhfjkhkjfnkl\r\nlkjklfjlkjlkc jjlkj\r\nl;ljlkj lkcjtkrjkjne', '2023-12-08 11:10:02', '2023-12-11 04:15:10'),
(7, 'Anuj kumar', 'ak@gmail.com', 1234567899, 'Transgender', 'Test', '2023-12-08 11:10:02', '2023-12-11 04:15:10');

CREATE TABLE `tblservices` (
  `ID` int(10) NOT NULL,
  `ServiceName` varchar(200) DEFAULT NULL,
  `Description` mediumtext DEFAULT NULL,
  `Cost` int(10) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `tblservices` (`ID`, `ServiceName`, `Description`, `Cost`, `CreationDate`) VALUES
(1, 'O3 Facial', 'Activated charcoal draws bacteria, toxins, dirt and oil from the skin.', 120, '2023-12-05 11:22:38'),
(2, 'Fruit Facial', 'If its a peel-off mask, it also works as an excellent exfoliator, ridding the skin of dead cells.', 500, '2023-12-05 11:22:38'),
(3, 'Charcol Facial', 'The end result is skin that is clean and clear. When used as a powder, charcoal masks can reach deep in your pores and suck out impurities with them.', 1000, '2023-12-05 11:22:38'),
(4, 'Deluxe Menicure', 'The end result is skin that is clean and clear. When used as a powder, charcoal masks can reach deep in your pores and suck out impurities with them.', 500, '2023-12-05 11:22:38'),
(5, 'Deluxe Pedicure', 'A pedicure is a therapeutic treatment for your feet that removes dead skin, softens hard skin and shapes and treats your toenails.', 600, '2023-12-05 11:22:38'),
(6, 'Normal Menicure', 'A pedicure is a therapeutic treatment for your feet that removes dead skin, softens hard skin and shapes and treats your toenails.', 300, '2023-12-05 11:22:38'),
(7, 'Normal Pedicure', 'A pedicure is a therapeutic treatment for your feet that removes dead skin, softens hard skin and shapes and treats your toenails.', 400, '2023-12-05 11:22:38'),
(8, 'Hair Cut', 'A hairstyle, hairdo, or haircut refers to the styling of hair, usually on the human scalp. Sometimes, this could also mean an editing of facial or body hair', 250, '2023-12-05 11:22:38'),
(9, 'Style Haircut', 'A hairstyle, hairdo, or haircut refers to the styling of hair, usually on the human scalp. Sometimes, this could also mean an editing of facial or body hair', 550, '2023-12-05 11:22:38'),
(10, 'Hair Wash', 'A hairstyle, hairdo, or haircut refers to the styling of hair, usually on the human scalp. Sometimes, this could also mean an editing of facial or body hair', 3999, '2023-12-05 11:22:38'),
(11, 'Loreal Hair Color(Full)', 'hgfhgj', 1200, '2023-12-05 11:22:38'),
(12, 'Body Spa', 'It is full body spa including hair wash', 1500, '2023-12-05 11:22:38'),
(15, 'ABC', 'gjhgjhgbkhhioljhoioi', 200, '2023-12-05 11:22:38'),
(16, 'Tradinational Cut', 'khghkhlkjlkjlkjflkrjnvoireyviutyouopyiuiosueoibvjmyruopo kjhkjhkhk kjh nkhu k iuyhiu kjhihiur', 45, '2023-12-05 11:22:38'),
(17, 'MUSTACHE TRIM', 'Trim Trim Trim', 85, '2023-12-05 11:22:38'),
(18, 'Beard Trim', 'Beard Trim', 10, '2023-12-05 11:22:38');

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblappointment`
--
ALTER TABLE `tblappointment`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblcustomers`
--
ALTER TABLE `tblcustomers`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblservices`
--
ALTER TABLE `tblservices`
  ADD PRIMARY KEY (`ID`);


--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblappointment`
--
ALTER TABLE `tblappointment`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblcustomers`
--
ALTER TABLE `tblcustomers`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblservices`
--
ALTER TABLE `tblservices`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

-- Today = 2026-04-10
-- Fills last 6 months (Nov 2025 - Apr 2026) with realistic appointment counts
-- Plus several appointments today for the "Appointments Today" list

INSERT INTO `tblappointment` (`AptNumber`, `Name`, `Email`, `PhoneNumber`, `AptDate`, `AptTime`, `Services`, `ApplyDate`, `Remark`, `Status`, `RemarkDate`) VALUES

-- November 2025 (slow month)
('APT101', 'Carlos Reyes', 'carlos@gmail.com', 9111111101, '2025-11-05', '09:00 AM', 'Hair Cut', '2025-11-04 08:00:00', 'Completed', 'Completed', '2025-11-05 10:00:00'),
('APT102', 'Anna Lee', 'anna@gmail.com', 9111111102, '2025-11-12', '10:00 AM', 'O3 Facial', '2025-11-11 09:00:00', 'Completed', 'Completed', '2025-11-12 11:00:00'),
('APT103', 'Marco Silva', 'marco@gmail.com', 9111111103, '2025-11-18', '02:00 PM', 'Beard Trim', '2025-11-17 10:00:00', 'Completed', 'Completed', '2025-11-18 15:00:00'),
('APT104', 'Nina Cruz', 'nina@gmail.com', 9111111104, '2025-11-25', '11:00 AM', 'Normal Menicure', '2025-11-24 08:30:00', NULL, 'Cancelled', '2025-11-25 11:30:00'),

-- December 2025 (holiday spike)
('APT201', 'Luis Torres', 'luis@gmail.com', 9222222201, '2025-12-02', '09:00 AM', 'Style Haircut', '2025-12-01 08:00:00', 'Completed', 'Completed', '2025-12-02 10:30:00'),
('APT202', 'Sofia Ramos', 'sofia@gmail.com', 9222222202, '2025-12-05', '01:00 PM', 'Fruit Facial', '2025-12-04 09:00:00', 'Completed', 'Completed', '2025-12-05 14:00:00'),
('APT203', 'Ben Santos', 'ben@gmail.com', 9222222203, '2025-12-08', '10:00 AM', 'Hair Color', '2025-12-07 10:00:00', 'Completed', 'Completed', '2025-12-08 12:00:00'),
('APT204', 'Mia Flores', 'mia@gmail.com', 9222222204, '2025-12-11', '03:00 PM', 'Body Spa', '2025-12-10 11:00:00', 'Completed', 'Completed', '2025-12-11 16:00:00'),
('APT205', 'Jake Rivera', 'jake@gmail.com', 9222222205, '2025-12-15', '11:00 AM', 'Deluxe Pedicure', '2025-12-14 08:00:00', 'Completed', 'Completed', '2025-12-15 13:00:00'),
('APT206', 'Clara Tan', 'clara@gmail.com', 9222222206, '2025-12-18', '02:00 PM', 'Charcol Facial', '2025-12-17 09:00:00', 'Completed', 'Completed', '2025-12-18 14:30:00'),
('APT207', 'Owen Lim', 'owen@gmail.com', 9222222207, '2025-12-22', '09:00 AM', 'Hair Wash', '2025-12-21 08:00:00', 'Completed', 'Completed', '2025-12-22 10:00:00'),
('APT208', 'Ella Gomez', 'ella@gmail.com', 9222222208, '2025-12-29', '04:00 PM', 'Normal Pedicure', '2025-12-28 10:00:00', NULL, 'Cancelled', '2025-12-29 16:30:00'),

-- January 2026 (new year dip)
('APT301', 'Aaron Perez', 'aaron@gmail.com', 9333333301, '2026-01-07', '10:00 AM', 'Beard Trim', '2026-01-06 09:00:00', 'Completed', 'Completed', '2026-01-07 11:00:00'),
('APT302', 'Isla Mendoza', 'isla@gmail.com', 9333333302, '2026-01-14', '01:00 PM', 'O3 Facial', '2026-01-13 10:00:00', 'Completed', 'Completed', '2026-01-14 14:00:00'),
('APT303', 'Finn Aquino', 'finn@gmail.com', 9333333303, '2026-01-21', '11:00 AM', 'Style Haircut', '2026-01-20 08:00:00', 'Completed', 'Completed', '2026-01-21 12:30:00'),
('APT304', 'Nora Bautista', 'nora@gmail.com', 9333333304, '2026-01-28', '03:00 PM', 'Deluxe Menicure', '2026-01-27 09:30:00', NULL, 'Cancelled', '2026-01-28 15:30:00'),

-- February 2026 (Valentine boost)
('APT401', 'Zoe Castillo', 'zoe@gmail.com', 9444444401, '2026-02-03', '09:00 AM', 'Fruit Facial', '2026-02-02 08:00:00', 'Completed', 'Completed', '2026-02-03 10:30:00'),
('APT402', 'Leo Garcia', 'leo@gmail.com', 9444444402, '2026-02-07', '02:00 PM', 'Hair Color', '2026-02-06 10:00:00', 'Completed', 'Completed', '2026-02-07 14:30:00'),
('APT403', 'Maya Reyes', 'maya@gmail.com', 9444444403, '2026-02-10', '10:00 AM', 'Body Spa', '2026-02-09 09:00:00', 'Completed', 'Completed', '2026-02-10 12:00:00'),
('APT404', 'Ian Santos', 'ian@gmail.com', 9444444404, '2026-02-13', '11:00 AM', 'Charcol Facial', '2026-02-12 08:30:00', 'Completed', 'Completed', '2026-02-13 13:00:00'),
('APT405', 'Ava Torres', 'ava@gmail.com', 9444444405, '2026-02-14', '01:00 PM', 'Deluxe Pedicure', '2026-02-13 11:00:00', 'Completed', 'Completed', '2026-02-14 14:00:00'),
('APT406', 'Sam Cruz', 'sam@gmail.com', 9444444406, '2026-02-20', '03:00 PM', 'Style Haircut', '2026-02-19 10:00:00', 'Completed', 'Completed', '2026-02-20 15:30:00'),
('APT407', 'Lily Flores', 'lily@gmail.com', 9444444407, '2026-02-26', '04:00 PM', 'Normal Menicure', '2026-02-25 09:00:00', NULL, 'Cancelled', '2026-02-26 16:30:00'),

-- March 2026 (steady growth)
('APT501', 'Ethan Lim', 'ethan@gmail.com', 9555555501, '2026-03-03', '09:00 AM', 'Hair Cut', '2026-03-02 08:00:00', 'Completed', 'Completed', '2026-03-03 10:00:00'),
('APT502', 'Grace Rivera', 'grace@gmail.com', 9555555502, '2026-03-06', '11:00 AM', 'O3 Facial', '2026-03-05 09:30:00', 'Completed', 'Completed', '2026-03-06 12:30:00'),
('APT503', 'Noah Tan', 'noah@gmail.com', 9555555503, '2026-03-10', '01:00 PM', 'Beard Trim', '2026-03-09 10:00:00', 'Completed', 'Completed', '2026-03-10 14:00:00'),
('APT504', 'Chloe Gomez', 'chloe@gmail.com', 9555555504, '2026-03-13', '02:00 PM', 'Body Spa', '2026-03-12 11:00:00', 'Completed', 'Completed', '2026-03-13 15:00:00'),
('APT505', 'Lucas Perez', 'lucas@gmail.com', 9555555505, '2026-03-17', '10:00 AM', 'Hair Color', '2026-03-16 08:30:00', 'Completed', 'Completed', '2026-03-17 11:30:00'),
('APT506', 'Emma Aquino', 'emma@gmail.com', 9555555506, '2026-03-20', '03:00 PM', 'Deluxe Menicure', '2026-03-19 10:00:00', 'Completed', 'Completed', '2026-03-20 16:00:00'),
('APT507', 'Aiden Mendoza', 'aiden@gmail.com', 9555555507, '2026-03-24', '11:00 AM', 'Fruit Facial', '2026-03-23 09:00:00', 'Completed', 'Completed', '2026-03-24 12:30:00'),
('APT508', 'Layla Bautista', 'layla@gmail.com', 9555555508, '2026-03-27', '04:00 PM', 'Normal Pedicure', '2026-03-26 11:00:00', NULL, 'Cancelled', '2026-03-27 16:30:00'),

-- April 2026 — TODAY (2026-04-10) appointments for the list card
('APT601', 'Trisha Villanueva', 'trisha@gmail.com', 9666666601, '2026-04-10', '09:00 AM', 'O3 Facial', '2026-04-09 08:00:00', 'Completed', 'Completed', '2026-04-10 10:00:00'),
('APT602', 'Marco Dela Cruz', 'marco2@gmail.com', 9666666602, '2026-04-10', '10:30 AM', 'Style Haircut', '2026-04-09 09:30:00', 'Completed', 'Completed', '2026-04-10 11:30:00'),
('APT603', 'Bea Santos', 'bea@gmail.com', 9666666603, '2026-04-10', '12:00 PM', 'Deluxe Pedicure', '2026-04-09 10:00:00', NULL, 'Confirmed', NULL),
('APT604', 'Jolo Reyes', 'jolo@gmail.com', 9666666604, '2026-04-10', '01:30 PM', 'Beard Trim', '2026-04-09 11:00:00', NULL, 'Confirmed', NULL),
('APT605', 'Camille Torres', 'camille@gmail.com', 9666666605, '2026-04-10', '03:00 PM', 'Body Spa', '2026-04-09 12:00:00', NULL, 'Pending', NULL);
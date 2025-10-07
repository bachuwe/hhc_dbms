-- SQL script to add date_of_birth and employment_status to MEMBERS table

ALTER TABLE MEMBERS 
ADD COLUMN date_of_birth DATE,
ADD COLUMN employment_status ENUM('APPRENTICESHIP', 'EMPLOYED', 'UNEMPLOYED');

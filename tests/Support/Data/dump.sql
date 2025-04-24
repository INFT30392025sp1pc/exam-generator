--Clear existing tables
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS trussimage;
DROP TABLE IF EXISTS student;
DROP TABLE IF EXISTS subject;
DROP TABLE IF EXISTS exam;
DROP TABLE IF EXISTS question;
DROP TABLE IF EXISTS parameter;


CREATE TABLE user (
                      user_ID int PRIMARY KEY,
                      user_password varchar(12),
                      first_name varchar(30),
                      last_name varchar(30),
                      user_email varchar(100),
                      user_role varchar(50)
);

CREATE TABLE trussimage (
                            truss_ID int PRIMARY KEY,
                            truss_name varchar(30),
                            truss_url varchar(100)
);

CREATE TABLE student (
                         student_ID int PRIMARY KEY,
                         first_name varchar(30),
                         last_name varchar(30),
                         student_email varchar(100)
);

CREATE TABLE subject (
                         subject_code varchar(20) PRIMARY KEY,
                         subject_name varchar(50),
                         subject_archive bool
);

CREATE TABLE exam (
                      exam_ID int PRIMARY KEY,
                      time_created datetime,
                      exam_year date,
                      exam_sp varchar(3),
                      last_modified datetime,
                      is_supplementary bool,
                      student_ID int,
                      subject_code varchar(20),
                      FOREIGN KEY (student_ID) REFERENCES student(student_ID),
                      FOREIGN KEY (subject_code) REFERENCES subject(subject_code)
);

CREATE TABLE question (
                          question_ID int PRIMARY KEY,
                          time_created datetime,
                          last_modified datetime,
                          contents text(10000),
                          exam_ID int,
                          FOREIGN KEY (exam_ID) REFERENCES exam(exam_ID)
);

CREATE TABLE parameter (
                           parameter_ID int PRIMARY KEY,
                           parameter_name varchar(30),
                           parameter_lower int,
                           parameter_upper int,
                           question_ID int,
                           FOREIGN KEY (question_ID) REFERENCES question(question_ID)
);
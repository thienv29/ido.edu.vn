CREATE TABLE wp_certificate (
    Id int NOT NULL AUTO_INCREMENT,
    Name text NOT NULL,
    TemplateSVG longtext NOT NULL,
    isDeleted boolean DEFAULT false,
    PRIMARY KEY (Id)
);
CREATE TABLE wp_user_form (
    Id int NOT NULL AUTO_INCREMENT,
    Name tinytext NOT NULL,
    Phone text NOT NULL,
    Email text NOT NULL,
    CertificateId int,
    isCertified boolean DEFAULT false,
    isDeleted boolean DEFAULT false,
    submittedAt datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (Id),
    CONSTRAINT fk_certificate FOREIGN KEY (CertificateId) REFERENCES wp_certificate(Id)
);


CREATE TABLE wp_certificated (
    Id int NOT NULL AUTO_INCREMENT,
    TemplateSVG longtext NOT NULL,
    isDeleted boolean DEFAULT false,
    createdAt datetime DEFAULT CURRENT_TIMESTAMP,
    userId int,
    PRIMARY KEY (Id),
    CONSTRAINT fk_user FOREIGN KEY (userId) REFERENCES wp_user_form(Id)
);
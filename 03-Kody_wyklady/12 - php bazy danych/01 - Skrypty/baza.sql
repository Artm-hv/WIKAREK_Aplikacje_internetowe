CREATE TABLE oceny (
    nr_albumu VARCHAR(6) PRIMARY KEY,
    nazwisko  VARCHAR(20),
    imie      VARCHAR(20),
    grupa     VARCHAR(11),
    stopien   VARCHAR(3)
);
insert into oceny values ("000001","Kowalski","Jan","11A","3,5");
insert into oceny values ("000002","Nowak","Marek","11A","4,5");
insert into oceny values ("000003","Mamla","Agnieszka","11A","5,0");
insert into oceny values ("000004","Kowalski","Darek","112A","2,0");
insert into oceny values ("000005","Suwak","tomek","112C","3,0");



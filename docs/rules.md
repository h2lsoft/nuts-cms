# RULES

## Directory structure

* **/app**
* **/cron** *(cron task script folder)*
* **/nuts** *(core folder)*
* **/library** *(library folder js, php, media)*
* **/themes** 	*(theme folder)*
* **/plugin**   *(plugin folder)*
* **/uploads**  *(plugin uploads folder)*
* **/vendor** 	*(vendor folder for composer)*
* **/x_includes** *(auto include files)*
	* **/nuts**  *(auto include files only for backend)*
	* **/www** *(auto include files only for frontend)*
	
	
## Database

Your table must respect these minimal rules :
 
* Primary Key must be called *ID*
* Your tables must have the following fields : 
```sql
Deleted ENUM (' YES ',' NO ') DEFAULT' NO 'INDEXED'
```

* Your foreign key must be called : *TableID*
* It's recommended to use PascalCase for table name: *my_table* becomes *MyTable*

*Example*
```sql
CREATE TABLE `MyTable`
(
	`ID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`Col1` VARCHAR(255) NULL,  
	`Deleted` ENUM('YES','NO') NOT NULL DEFAULT 'NO', 
	PRIMARY KEY (`ID`), 
	INDEX `Deleted` (`Deleted`)
);
```

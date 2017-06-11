<?php
echo 'dz9oia<br>';
echo 'Forget '||'Code';
exit;



//PDO 33b rolback tanzakcjie multi bez specjalnej klasy

/**
CREATE TABLE `klienci` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`imie` VARCHAR(50) NULL DEFAULT NULL,
	`nazwisko` VARCHAR(50) NULL DEFAULT NULL,
	`adres` CHAR(50) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COMMENT='tabela wykorzystana przy testach tranzakcji'
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=13;
*/

    $PDO = NULL;
    $pdo_dsn = 'mysql:host=localhost;dbname=test';
    $pdo_persistence = array( PDO::ATTR_PERSISTENT => true );
    $db_user = 'root';
    $db_pass = '';
   

   $pdo = new PDO($pdo_dsn, $db_user, $db_pass, $pdo_persistence);
	
	

    
$pdo->beginTransaction();
 $pdo->exec("SAVEPOINT LEVEL0");
try {
    $stmt = $pdo->prepare("INSERT INTO klienci(imie,nazwisko,adres) VALUES(:imie,:nazwisko,:adres)");
	$stmt->execute(array(':imie' => 'emi12a', ':nazwisko' => 'joo12a', ':adres' => 'lipowa12a'));

    $pdo->beginTransaction();
    $pdo->exec("SAVEPOINT LEVEL1");
    try {
    
    // blad rolback sie tu robi - ale wykonuje pierwsza operacje a nie powinien wg. mnie 
    
        $stmt = $pdo->prepare("INSERT INTO klienci(imie,nazwisko,adres) VALUESm(:imie,:nazwisko,:adres)");
	$stmt->execute(array(':imie' => 'emi12b', ':nazwisko' => 'joo12b', ':adres' => 'lipowa12b'));

        $pdo->commit();
         $pdo->exec("SAVEPOINT LEVEL1");
    } catch(PDOException $e) {
        // If this statement fails, rollback...
        // NOTE: This will only rollback statements made in the
        //       inner try { block and not the outer one.
        $pdo->rollBack();
        $pdo->exec("ROLLBACK TO SAVEPOINT LEVEL1");
        echo 'rolback 1';
    }

    $pdo->commit();
    $pdo->exec("RELEASE SAVEPOINT LEVEL0");
} catch (PDOException $e) {
    $pdo->rollBack();
    $pdo->exec("ROLLBACK TO SAVEPOINT LEVEL0");
    echo 'rolback 2';
}

    //tranzakcja1($dbh);
    
?>


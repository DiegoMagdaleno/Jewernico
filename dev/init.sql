SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';


DROP SCHEMA IF EXISTS `jewernico_gina` ;

CREATE SCHEMA IF NOT EXISTS `jewernico_gina` DEFAULT CHARACTER SET utf8 COLLATE utf8_spanish_ci ;
USE `jewernico_gina` ;

-- -----------------------------------------------------
-- Table `jewernico_gina`.`categoria`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jewernico_gina`.`categoria` ;

CREATE TABLE IF NOT EXISTS `jewernico_gina`.`categoria` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(45) COLLATE 'utf8mb3_spanish_ci' NOT NULL,
  PRIMARY KEY (`Id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `jewernico_gina`.`pregunta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jewernico_gina`.`pregunta` ;

CREATE TABLE IF NOT EXISTS `jewernico_gina`.`pregunta` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Pregunta` VARCHAR(128) NOT NULL,
  `Respuesta` VARCHAR(1024) NOT NULL,
  PRIMARY KEY (`Id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `jewernico_gina`.`usuario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jewernico_gina`.`usuario` ;

CREATE TABLE IF NOT EXISTS `jewernico_gina`.`usuario` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(45) COLLATE 'utf8mb3_spanish_ci' NOT NULL,
  `ApellidoMaterno` VARCHAR(45) COLLATE 'utf8mb3_spanish_ci' NOT NULL,
  `ApellidoPaterno` VARCHAR(45) COLLATE 'utf8mb3_spanish_ci' NOT NULL,
  `CorreoElectronico` VARCHAR(120) NOT NULL,
  `Password` VARCHAR(255) COLLATE 'utf8mb3_spanish_ci' NOT NULL,
  `NivelPermisos` INT UNSIGNED NOT NULL DEFAULT '0',
  `IntentosDeLogin` INT(1) UNSIGNED NULL DEFAULT '0',
  `IdPregunta` INT,
  PRIMARY KEY (`Id`, `IdPregunta`),
  UNIQUE INDEX `CorreoElectronico_UNIQUE` (`CorreoElectronico` ASC) VISIBLE,
  INDEX `fk_usuario_pregunta1_idx` (`IdPregunta` ASC) VISIBLE,
  CONSTRAINT `fk_usuario_pregunta1`
    FOREIGN KEY (`IdPregunta`)
    REFERENCES `jewernico_gina`.`pregunta` (`Id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `jewernico_gina`.`compra`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jewernico_gina`.`compra` ;

CREATE TABLE IF NOT EXISTS `jewernico_gina`.`compra` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Fecha` DATE NOT NULL,
  `IdUsuario` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_Compras_Clientes1_idx` (`IdUsuario` ASC) VISIBLE,
  CONSTRAINT `fk_Compras_Clientes1`
    FOREIGN KEY (`IdUsuario`)
    REFERENCES `jewernico_gina`.`usuario` (`Id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `jewernico_gina`.`material`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jewernico_gina`.`material` ;

CREATE TABLE IF NOT EXISTS `jewernico_gina`.`material` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(45) COLLATE 'utf8mb3_spanish_ci' NOT NULL,
  PRIMARY KEY (`Id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `jewernico_gina`.`producto`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jewernico_gina`.`producto` ;

CREATE TABLE IF NOT EXISTS `jewernico_gina`.`producto` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Nombre` VARCHAR(128) COLLATE 'utf8mb3_spanish_ci' NOT NULL,
  `Precio` DOUBLE UNSIGNED NOT NULL,
  `Descripcion` VARCHAR(1024) NULL DEFAULT NULL,
  `IdMaterial` INT NOT NULL,
  `IdCategoria` INT NOT NULL,
  `Stock` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_Producto_Material1_idx` (`IdMaterial` ASC) VISIBLE,
  INDEX `fk_Producto_Categoria1_idx` (`IdCategoria` ASC) VISIBLE,
  CONSTRAINT `fk_Producto_Categoria1`
    FOREIGN KEY (`IdCategoria`)
    REFERENCES `jewernico_gina`.`categoria` (`Id`),
  CONSTRAINT `fk_Producto_Material1`
    FOREIGN KEY (`IdMaterial`)
    REFERENCES `jewernico_gina`.`material` (`Id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `jewernico_gina`.`contener`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jewernico_gina`.`contener` ;

CREATE TABLE IF NOT EXISTS `jewernico_gina`.`contener` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `IdCompra` INT NOT NULL,
  `IdProducto` INT NOT NULL,
  `Cantidad` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_Compra_has_Producto_Producto1_idx` (`IdProducto` ASC) VISIBLE,
  INDEX `fk_Compra_has_Producto_Compra1_idx` (`IdCompra` ASC) VISIBLE,
  CONSTRAINT `fk_Compra_has_Producto_Compra1`
    FOREIGN KEY (`IdCompra`)
    REFERENCES `jewernico_gina`.`compra` (`Id`),
  CONSTRAINT `fk_Compra_has_Producto_Producto1`
    FOREIGN KEY (`IdProducto`)
    REFERENCES `jewernico_gina`.`producto` (`Id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `jewernico_gina`.`telefono`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jewernico_gina`.`telefono` ;

CREATE TABLE IF NOT EXISTS `jewernico_gina`.`telefono` (
  `Telefono` VARCHAR(11) COLLATE 'utf8mb3_spanish_ci' NOT NULL,
  `IdUsuario` INT NOT NULL,
  PRIMARY KEY (`Telefono`, `IdUsuario`),
  INDEX `fk_Telefonos_Clientes_idx` (`IdUsuario` ASC) VISIBLE,
  CONSTRAINT `fk_Telefonos_Clientes`
    FOREIGN KEY (`IdUsuario`)
    REFERENCES `jewernico_gina`.`usuario` (`Id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `jewernico_gina`.`direccion`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jewernico_gina`.`direccion` ;

CREATE TABLE IF NOT EXISTS `jewernico_gina`.`direccion` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Calle` VARCHAR(50) NOT NULL,
  `NumeroDeCasa` VARCHAR(10) NOT NULL,
  `Ciudad` VARCHAR(50) NOT NULL,
  `CodigoPostal` VARCHAR(20) NOT NULL,
  `Pais` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`Id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `jewernico_gina`.`tener`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `jewernico_gina`.`tener` ;

CREATE TABLE IF NOT EXISTS `jewernico_gina`.`tener` (
  `IdUsuario` INT NOT NULL,
  `IdDireccion` INT NOT NULL,
  PRIMARY KEY (`IdUsuario`, `IdDireccion`),
  INDEX `fk_usuario_has_direccion_direccion1_idx` (`IdDireccion` ASC) VISIBLE,
  INDEX `fk_usuario_has_direccion_usuario1_idx` (`IdUsuario` ASC) VISIBLE,
  CONSTRAINT `fk_usuario_has_direccion_usuario1`
    FOREIGN KEY (`IdUsuario`)
    REFERENCES `jewernico_gina`.`usuario` (`Id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuario_has_direccion_direccion1`
    FOREIGN KEY (`IdDireccion`)
    REFERENCES `jewernico_gina`.`direccion` (`Id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

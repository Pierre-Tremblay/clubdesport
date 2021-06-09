<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'tonclubdesportfavoris' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', '' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'fz>mHE:wRCPvGRcaaNk _~h~Kt:`n+z;iXxe(pm4*p/V{u0X+;:`j%<3Y`UE4zr-' );
define( 'SECURE_AUTH_KEY',  '.0(:Is*>SDO}zqQ}EY:M;|3I6=Rs,;N`Xu~<_:^} sV,^gPv:$MkDJsdv;9w-;_A' );
define( 'LOGGED_IN_KEY',    'CCd]R/Xt3.+W]+xk^H`Iwt9IZi~Ahi91e~_i>2ePf6F1)`1[C~.y8c6ixKi:I2l{' );
define( 'NONCE_KEY',        'lMvI*|wGAAlDZU,+6EJL2dlHsoob/A*2fj$[;QKIJiu69V<>-u4^@YG-foSBI|1m' );
define( 'AUTH_SALT',        ',hn}oN/lQRi6^5zaE$S<h[+Mi:$V)Mo|?-9<%-6]j(~x!wfz~q rKXQ,wY&#^6OE' );
define( 'SECURE_AUTH_SALT', '_^_;`L=$H=nV:25SHwdwCf9Sm(>MTyj)rFA^<>BI|d5q]&P%NR=(b>uf7Yt(?/lB' );
define( 'LOGGED_IN_SALT',   '~<O}|bT&7|m`8l9j$B<ok[e)SzbMB0g~1?Tckc)yQa~(ycj2Cq?;1C.J:8R3wHu|' );
define( 'NONCE_SALT',       'E9{)w|FSjy2,)Qsv^6U CI21xDt@#*~TQ5C) V={``}SIz5@IDKEStl)q:|+&%m&' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');

<?php

  session_start();
  $customer = $_GET['customer'];
  
  include "../" . $customer . "/config/custom_cfg.php";  
  include "config/common_cfg.php";
  include "param.php";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $bdd);
  // Check connection
  if ($conn->connect_error) 
    die("Connection failed: " . $conn->connect_error);    

  $method = isset($_GET ['method']) ? $_GET ['method'] : '0';
  $table = isset($_GET ['table']) ? $_GET ['table'] : '0';

  session_start();
  
  if (strcmp($_SESSION[$customer .'_mail'],'oui') == 0)
  {
    header('LOCATION: carte.php?method=' . $method . '&table=' . $table . '&customer=' . $customer);
    exit();
  }
?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--<link rel="stylesheet" href="css/style.css">-->
    <!--<link rel="stylesheet" href="css/custom.css">-->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
  </head>
  <body>
  <?php
    echo '<input class="inpmove revenir" type="button" value="Revenir sur la commande" onclick="';
    echo 'window.location.href = \'getinfo.php?method=';
    echo $method;
    echo '&table=';
    echo $table;
    echo '&customer=';
    echo $customer;
    echo '\'"';
  ?>
<br>
<p style="text-align: justify; text-justify: inter-word;">
Praticboutic<br>
Conditions générales de vente<br>
<br>
Bienvenue sur notre site web Praticboutic.fr et dans nos services. Cette page présente les conditions aux-quelles nos établissements partenaires vous fournissent leurs produits figurant sur notre site internet.<br>
<br>
Merci de lire attentivement ces conditions avant de passer une commande via notre site. En accédant à notre site et en passant une commande, vous acceptez d’être lié sans réserve par les présentes conditions et par notre politique de conditions d’utilisation. Si vous avez des questions au sujet de ces  conditions, veuillez nous contacter à l’adresse contact@Praticboutic.fr avant de passer commande. Si vous n’acceptez pas intégralement ces conditions, n’utilisez pas notre Service.<br>
1. À notre sujet<br>
Praticboutic.fr est un site web exploité par LEGRAND IKONE PUBLICITE, société sous la forme SARL, au capital de 5120 euros dont le siège social est situé au 797, route des Grands Jardins - 84300 LES TAILLADES, et immatriculée au RCS en France sous le n° 502 122 690 00013<br>
2. Objectif<br>
Notre Service a pour but la fourniture d’un service simple et pratique en vous mettant en relation avec nos établissements partenaires autorisés à proposer  leurs produits et en vous permettant de leurs passer une commande de Produits.<br>
<br>
Praticboutic agit en qualité d’agent pour le compte de l’Établissement Partenaire qui propose des Produits, et reçoit les commandes que vous passez. Une fois que vous avez passé une commande, vos Produits vous seront servis par notre Établissement Partenaire sur place à table, à emporter ou en livraison.<br>
3. Disponibilité des services<br>
Le Service Praticboutic est disponible selon les heures d’ouverture des restaurateurs et varie en fonction des habitudes commerciales locales et de la  disponibilité de nos Établissements Partenaires.<br>
Le service pourra être désactivé de manière ponctuelle ou définitive par l’Établissement et/ou par l’équipe Praticboutic sans avoir à fournir de justification ou de raisons valables à cette désactivation aux utilisateurs du service.<br>
4. Commandes<br>
Chaque commande passée par le biais de notre plateforme sera transmise à l’Établissement Partenaire pour obtenir une confirmation de sa part. La commande est définitive une fois qu’elle a été acceptée par l’Établissement Partenaire.<br>
<br>
Le contrat de fourniture de tout Produit que vous commandez par notre intermédiaire est conclu entre vous et l’Établissement Partenaire et n’est conclu qu’à compter de l’envoi du mail de confirmation.<br>
<br>
A travers le processus de prise de commande, vous acceptez expressément l’application des présentes conditions générales de prestations de service.<br>
<br>
5. Repas<br>
Tous les Produits sont proposés sous réserve de leur disponibilité.<br>
Nos Partenaires peuvent utiliser des noix dans la préparation de certains Repas.<br>
Veuillez-vous référez à notre Établissement Partenaire avant de passer commande si vous souffrez d’allergie.<br>
<br>
Praticboutic ne peut pas garantir que les Produits vendus par nos Établissements Partenaires sont sans allergène.<br>
<br>
6. Vente d’alcool<br>
<br>
6.1 Les boissons alcoolisées ne peuvent être vendues qu’aux personnes de plus de 18 ans et une preuve d’identité peut être exigée par l’Établissement Partenaire (article L3342-1 du Code de la santé publique). L’Établissement Partenaire pourra refuser de fournir de l’alcool à toute personne qui semble avoir moins de 18 ans ou qui est ou semble sous l’influence de l’alcool ou de médicaments. En passant une commande qui contient de l’alcool, vous confirmez que vous avez au moins 18 ans. Toute violation du présent article engage votre responsabilité personnelle exclusive.<br>
<br>
6.2 L’abus d’alcool est dangereux pour la santé. Sachez consommer et apprécier avec modération. Par le seulfait de passer commande sur notre « site internet », vous renoncez expressément au bénéfice de l’article 1587 du Code civil qui prévoit que la conclusion de la vente de vin ne devient définitive qu’après  dégustation et agrément de l’acheteur.<br>
6.3 Si vous êtes identifié comme mineur, l’Établissement Partenaire procèdera uniquement au service des Produits non alcoolisés et reprendra l’intégralité des Produits alcoolisés. L’Établissement n’aura alors aucune obligation de rembourser au client les produits préparés mais non délivrés en raison du non-respect des présentes conditions.<br>
<br>
7. Disponibilité et service<br>
Notre objectif est de garantir que vous obteniez le meilleur service possible. Malheureusement, les choses ne se déroulent pas toujours comme prévu et des aléas peuvent parfois nous empêcher d’atteindre nos objectifs, ou ceux de nos Établissements Partenaires, à cet égard nous faisons tout notre possible pour garantir la livraison de vos Produits dans les meilleurs délais.<br>
Le délai de livraison de votre commande est établi en fonction du nombre de commandes et des conditions auxquelles est soumis l’Établissement Partenaire à ce moment.<br>
8. Annulation<br>
l’établissement Partenaire peut ou non accepter une commande(«Commande acceptée»).<br>
Praticboutic et l’établissement Partenaire peuvent annuler une commande et vous en informent dans ce cas.<br>
Les commandes annulées conformément à cette clause ne vous sont pas facturées.<br>
<br>
Tout règlement effectué avant l’annulation d’une commande par Praticboutic ou par un Établissement Partenaire est généralement remboursé par le même moyen que celui que vous avez utilisé pour payer votre commande.<br>
Chaque commande annulée après être devenue une Commande acceptée vous est facturée. Praticboutic seul établit si une commande est ou non une Commande acceptée grâce à l’analyse de notre historique d’envoi de SMS. <br>
Vous ne disposez pas de droit de rétractation, en vertu des dispositions de l’article L121-21-8 alinéa 4 du Code de la consommation, les Repas, objets du contrat étant des denrées périssables.<br>
9. Prix et règlement<br>
<br>
Le prix des Produits est indiqué dans notre service. Les prix sont TTC et incluent la TVA et les frais de service. Les prix varient selon les menus. Les prix sont susceptibles d’être révisés à tout moment mais les modifications ne s’appliquent pas aux commandes pour lesquelles vous avez reçu le sms de confirmation.<br>
Malgré tous nos efforts, le prix de certains Produits figurant dans notre service peut être erroné. l’établissement partenaire concerné vérifie normalement ses prix dans le cadre du processus de passation de commande.<br>
Tous les Produits peuvent être réglés par carte bancaire par le biais de notre Service. Sont acceptés comme moyens de paiement : les cartes de crédit, carte bancaire, (visa, mastercard etc.). et d’autres moyen de paiement mobile seront disponible au fur et à mesure sur notre plateforme. Précisez si vous avez un système de paiement sécurisé.<br>
<br>
En acceptant de contracter sur la base des présentes conditions, vous reconnaissez explicitement votre obligation de paiement. Une fois que votre commande a été confirmée, votre carte de crédit reçoit une autorisation de débit et le montant est réservé pour être débité.<br>
Dans le cas ou dans un intervalle de 24h, plusieurs commandes seraient effectuées par le même client et avec le même moyen de paiement, Praticboutic se réserve le droit de regrouper ces commandes et de les débiter sous la forme d’une seule transaction correspondant au montant total de ces commandes.
Le règlement est effectué directement à Praticboutic qui le transmet ensuite à l’établissement Partenaire. Nous sommes autorisés par nos Établissements partenaires à accepter le règlement pour leur compte et le règlement du prix des produits à notre bénéfice vous dégage de vos obligations de payer ce prix à l’établissement partenaire.<br>
<br>
10. Notre Responsabilité<br>
<br>
Dans les limites autorisées par la loi, Praticboutic fournit son Service et son contenu «en l’état» et «en fonction de la disponibilité».<br>
<br>
Selon les dispositions ci-dessous, ni Praticboutic ni quelque Établissement partenaire que ce soit n’ont de responsabilité vis-à-vis de vous en ce qui  concerne des pertes directes, indirectes, particulières ou des dommages d’ordre contractuel, dus à un préjudice (dont la négligence) ou par ailleurs découlant de votre utilisation ou de votre incapacité à utiliser notre Service.<br> 
<br>
Si la responsabilité de Praticboutic ou de l’établissement partenaire est engagée vis-à-vis de vous, notre responsabilité totale cumulée est limitée au prix d’achat des Repas que vous avez payés dans votre commande.<br>
<br>
Cela ne comprend pas et ne limite en aucune manière la responsabilité de Praticboutic ou d’un Établissement partenaire sur un sujet pour lequel il serait illégal que nous excluions ou tentions d’exclure ou qu’il exclue ou tente d’exclure notre ou sa responsabilité, en particulier, la responsabilité dans un  décès ou des blessures corporelles dû (dues) à la négligence ou à une fraude ou à une déclaration frauduleuse.<br>
<br>
Le contrat de vente est conclu entre vous et l’établissement. l’établissement partenaire que vous choisissez est un commerçant indépendant, distinct de notre société et sur lequel nous n’avons aucun contrôle. En conséquence, en dehors des strictes conditions légales, notre responsabilité ne saurait être engagée à ce titre.<br>
<br>
l’établissement partenaire, vendeur, est tenu des défauts de conformité du bien au contrat dans les conditions de l’article L 211-4 du Code de la consommation et des défauts de la chose vendue en cas de vices cachés (article 1641 Code civil) et de la garantie légale de conformité (article L211-4 et suivant Code de la consommation).<br>
<br>
11. Évènements indépendants de notre volonté<br>
Ni vous, ni Praticboutic, ni l’établissement partenaire n’êtes responsables vis-à-vis des autres parties d’un retard ou d’une absence d’exécution de ses obligations aux termes du présent contrat si ledit retard ou ladite absence est indépendant(e) de sa volonté dont, de manière non exhaustive, les événements suivants : catastrophe naturelle, disposition gouvernementale, guerre, incendie, inondation, explosion ou mouvements populaires.<br>
<br>
12. Disjonction<br>
<br>
Si une disposition du présent contrat est jugée illégale ou inapplicable, la force exécutoire intégrale des autres dispositions demeure.<br>
<br>
13. Contrat indivisible<br>
Les présentes conditions contiennent l’intégralité du contrat entre les parties relatif à son objet et remplacent l’ensemble des contrats et accords antérieurs entre les parties relatifs à cet objet.<br>
14. Notre droit de modifier ces conditions<br>
Praticboutic peut réviser les présentes conditions à tout moment en modifiant cette page. Vous êtes censévérifier cette page de temps à autre pour prendre connaissance des modifications que nous avons apportées car elles sont contraignantes pour vous.<br>
15. Droit et compétence<br>
Les tribunaux français ont compétence pour toutes les plaintes découlant de ou liées à une utilisation de nos Services. Les présentes conditions d’utilisation et tout conflit ou plainte découlant de ou lié(e) à ces conditions d’utilisation ou leur objet ou formation (dont les conflits ou plaintes non contractuel(le)s) doivent être régis par et interprétés conformément au droit français. Conformément aux dispositions du Code de la consommation, il vous est possible de recourir à un mode alternatif de règlement des litiges ou à une procédure de médiation conventionnelle. La recherche d’une telle solution amiable interrompt les délais pour agir.<br>
<br>
A défaut, la juridiction compétente sera déterminée par les règles de droit commun.<br>
Conditions d’utilisation de Praticboutic pour le site web et Les applications<br>
Cette page (en association avec les documents auxquels elle fait référence) présente les conditions d’utilisation qui vous permettent d’utiliser notre site web www.Praticboutic.fr (notre «Site») ou les applications que nous mettons à disposition par le biais d’une boutique d’applications ou autre (notre «Service»), que ce soit comme hôte ou comme utilisateur inscrit. Veuillez lire attentivement ces conditions d’utilisation avant de commencer à utiliser notre Site ou notre Service. En accédant à notre Site ou en utilisant notre Service, vous indiquez que vous acceptez ces conditions d’utilisation et que vous vous engagez à les respecter. Si vous n’êtes pas d’accord avec ces conditions d’utilisation, n’accédez pas à notre Site ou n’utilisez pas notre Service.<br>
1. A Notre Sujet<br>
Praticboutic.fr est un Site web exploité par SARL LEGRAND IKONE PUBLICITE<br>
2. Accès à notre site ou à nos services<br>
L’accès à notre Site et à notre Service est autorisé de manière temporaire et nous nous réservons le droit de supprimer ou de modifier sans préavis l’accès à notre Site ou à notre Service (voir ci-dessous). Nous ne sommes pas responsables si, pour quelque motif que ce soit, notre Site ou notre Service n’est pas disponible à un moment donné ou pendant une période donnée. Le cas échéant, nous pouvons limiter l’accès à certaines parties de notre Site ou de notre Service ou l’accès à l’ensemble de notre Site ou Service aux utilisateurs inscrits. Vous êtes responsable de la préservation de la confidentialité de vos informations de connexion et des activités de votre compte. Si vous avez des inquiétudes au sujet de vos informations de connexion ou si vous pensez qu’elles ont été piratées, prenez immédiatement contact avec contact@Praticboutic.fr pour nous en informer. Nous pouvons désactiver votre compte à tout moment.<br>
3. Utilisation acceptable<br>
Vous ne pouvez utiliser notre Service qu’à des fins légales. Vous ne pouvez utiliser notre Site ou notre Service<br>
<br>
d’aucune manière qui contrevienne à une loi ou réglementation locale, nationale ou internationale applicable ni pour envoyer, recevoir sciemment, télécharger, utiliser ou réutiliser des éléments qui ne sont pas conformes à nos normes en matière de contenu figurant dans la clause 5 ci-dessous. Vous vous engagez éga-
lement à ne pas accéder sans autorisation à, endommager ou perturber une partie de notre Site ou de notre Service ou un réseau ou un matériel utilisé dans la fourniture de notre Service.<br>
4. Fonctions interactives de notre site<br>
Nous pouvons le cas échéant offrir des fonctions qui vous permettent d’interagir par le biais de notre Site ou de notre Service, comme des groupes de  discussion. En règle générale, nous ne modérons aucun Service interactif que nous fournissons, bien que nous puissions supprimer un contenu qui ne  respecterait pas ces conditions d’utilisation, conformément aux dispositions de la section 6. Si nous décidons de modérer un Service interactif, nous le mentionnons clairement avant que vous utilisiez le Service et vous offrons normalement un moyen de prendre contact avec l’animateur en cas de doute ou de difficulté.<br>
5. Normes en matière de contenu<br>
Ces normes en matière de contenu s’appliquent à tous les éléments par lesquels vous contribuez à notre Service (les «Contributions») et aux éventuels Services interactifs qui y sont associés. Vous devez respecter l’esprit et la lettre des normes suivantes. Les normes s’appliquent à chaque partie des Contributions et à leur globalité. Les Contributions doivent être précises (lorsqu’elles exposent des faits), être sincères (lorsqu’elles exposent des convictions) et  respecter la législation applicable en France et dans le pays d’où elles sont émises.<br>
Les Contributions ne doivent pas :<br>
<br>
contenir des éléments diffamatoires pour une personne, obscènes, injurieux, haineux ou incendiaires, promouvoir des scènes explicites sur le plan sexuel ou promouvoir la violence ou promouvoir la discrimination sur la base de la race, du sexe, de la religion, de la nationalité, du handicap, de l’orientation sexuelle ou de l’âge; enfreindre des droits d’auteur, droits sur des bases de données ou marques de commerce d’un tiers ; être susceptibles de tromper un tiers ou être en infraction avec une obligation légale vis-à-vis d’un tiers, comme une obligation contractuelle ou une obligation de discrétion ou promouvoir une activité illégale ; être menaçantes, porter atteinte à ou violer l’intimité d’un tiers, provoquer une gêne, un dérangement ou une anxiété inutile ou être susceptibles de harceler, perturber, embarrasser, alarmer ou gêner un tiers ; être utilisées pour se faire passer pour quelqu’un d’autre ou usurper votre identité ou tromper sur votre affiliation avec une personne ou donner l’impression qu’elles émanent de nous, si tel n’est pas le cas préconiser, promouvoir ou aider à un acte illégal comme (à titre d’exemple uniquement) une violation de droit d’auteur ou un piratage d’ordinateur.<br>
6. Suspension et résiliation<br>
Le non-respect de la section 3 (Utilisation acceptable) et/ou 5 (Normes en matière de contenu) des présentes conditions d’utilisation constitue une violation déterminante des conditions d’utilisation et peut nous amener à prendre tout ou partie des mesures suivantes : suppression immédiate, temporaire ou permanente de votre droit d’utiliser notre Service ; suppression immédiate, temporaire ou permanente des contributions ou éléments téléchargés par vous sur notre Service ;envoi d’un avertissement à votre encontre ;action en justice contre vous, comprenant une procédure en vue du remboursement de tous les coûts (dont, de manière non exhaustive, les coûts administratifs et les frais de justice raisonnables) entraînés par l’infraction ;communication de ces informations aux autorités chargées de l’application des lois si nous l’estimons légitimement nécessaire.<br>
<br>
Les réactions décrites dans cette clause ne sont pas exhaustives et nous pouvons prendre toutes les autres mesures que nous jugeons légitimement adaptées. Le nom du responsable de la publication et les motifs de retrait apparaitront sur le Site.<br>
7. Droits de propriété intellectuelle<br>
Nous sommes le propriétaire ou le détenteur de licence de tous les droits de propriété intellectuelle de notre Site et de notre Service et des éléments publiés sur ce Site (à l’exception de vos contributions). Ces travaux sont protégés par les lois et traités sur le droit d’auteur dans le monde entier. Tous ces droits sont réservés. <br>
Vous n’avez pas le droit de copier, reproduire, republier, télécharger, envoyer, diffuser, transmettre, communiquer au public ou utiliser un contenu de notre Site de quelque manière que ce soit, sauf pour votre usage personnel sans caractère commercial.<br>
8. Confiance dans les Informations affichées sur le site<br>
Les commentaires et autres éléments affichés sur notre Service ne sont pas destinés à être considérés comme des conseils auxquels on peut faire confiance. Nous déclinons par conséquent toute responsabilité au titre de la confiance accordée à ces éléments par les visiteurs de notre Service ou par quiconque  susceptible d’être informé de son contenu.<br>
9. Actualisations régulières de notre site et de notre service<br>
Nous souhaitons mettre régulièrement à jour notre Site et notre Service et pouvons en modifier le contenu à tout moment. En cas de nécessité, nous pouvons suspendre l’accès à notre Site et à notre Service ou les fermer pour une durée indéterminée. Les éléments de notre Site ou notre Service peuvent être dépassés à un moment donné et nous ne sommes en aucun cas tenus d’actualiser ces éléments.<br>
<br>
10. Notre responsabilité<br>
Nous avons créé et entretenu très soigneusement notre Site et notre Service. Cependant, nous ne sommes pas responsables des éventuelles erreurs ou omissions en rapport avec ledit contenu et des éventuels problèmes techniques que vous pouvez rencontrer avec notre Site ou notre Service. Si nous sommes informés d’inexactitudes sur notre Site ou dans notre Service, nous essaierons de les corriger dès que cela sera raisonnablement possible. Dans les limites autorisées par la loi, nous excluons toute responsabilité (contractuelle, au titre de la négligence ou autre) dans une perte ou un dommage que vous ou un tiers pourriez subir en liaison avec notre Site, notre Service et tout Site web lié à notre Site et les éléments affichés sur le Site. Cela ne concerne pas notre responsabilité en matière de décès ou de blessures corporelles dû(dues) à notre négligence ni notre responsabilité en matière de déclaration frauduleuse ou de déclaration inexacte sur un sujet fondamental ni une autre responsabilité qui ne peut pas être exclue ou limitée selon la législation applicable.
11. Informations relatives à vous et à vos visites sur notre site et à l’utilisation de notre service. Nous collectons certaines données à votre sujet dans le cadre de votre utilisation de notre Service. Ce point est décrit de manière plus détaillée dans notre politique de respect de la vie privée. Notre numéro de déclaration à la CNIL est en cours d’attribution. Concernant les cookies, un bandeau informe les internautes de leur finalité, qui pourront les refuser ou les accepter. La durée maximum de ce consentement étant de 13 mois.<br>
12. Téléchargement de données sur notre site et notre service<br>
13. Liens de notre site<br>
<br>
Lorsque notre Site contient des liens vers d’autres sites et ressources fournis par des tiers, ces liens sont donnés à titre d’information uniquement. Nous ne contrôlons pas le contenu de ces Sites et ressources et déclinons toute responsabilité liée à ce contenu ou en matière de perte ou dommage susceptible de découler de votre utilisation de ce contenu.<br>
14. Compétence et législation applicable<br>
Les tribunaux français ont compétence pour toutes les plaintes découlant de ou liées à une consultation de notre Site ou à une utilisation de nos Services. Les présentes conditions d’utilisation et tout conflit ou plainte découlant de ou lié(e) à ces conditions d’utilisation ou leur objet ou formation (dont les conflits ou plaintes non contractuel(le)s) doivent être régis par et interprétés conformément au droit français.<br>
15. Modifications<br>
Nous pouvons réviser les présentes conditions d’utilisation à tout moment en modifiant cette page. Vous êtes censé vérifier cette page de temps à autre pour prendre connaissance des modifications que nous avons apportées car elles sont contraignantes pour vous.<br>
16. Vos préoccupations<br>
Si vous avez des préoccupations au sujet de données qui figurent dans notre Service, veuillez prendre contact par email sur contact@Praticboutic.fr<br>
Ensemble des conditions générales<br>
Sauf indication contraire, toute réduction, tout bon ou code, sera applicable uniquement sur les premières<br>
commandes effectuées par de nouveaux clients de Praticboutic.<br>
Les nouveaux clients sont autorisés à utiliser un seul bon ou code lorsqu’ils passent leur première commande.<br>
Les ordres ultérieurs ne seront pas éligibles pour toutes les réductions, bons ou codes réservés aux nouveaux clients.<br>
Un nouveau client est défini comme étant une personne qui s’inscrit, qui choisit une sélection de repas et qui saisit le code promotionnel ou de réduction  indiqué sur une publicité, un coupon ou un dépliant, lors de son passage en caisse ou durant la commande.<br>
Un montant minimum est nécessaire afin de valider et d’utiliser toute remise, bon ou code.<br>
Sauf indication contraire, toutes les réductions, bons ou codes doivent être utilisés dans un mois civil.<br>
<br>
Toutes les dates de validité des promotions sont spécifiées sur les annonces, coupons ou dépliants. Veuillez-vous reporter aux conditions résumées sur ces supports pour obtenir des informations exactes et spécifiques concernant cette promotion et sa période promotionnelle.<br>
<br>
Tout alcool sera fourni uniquement aux personnes âgées de 18 ans ou plus.<br>
Dans le cas où une commande alcoolisée serait opérée par un mineur ou une personne ne pouvant justifier de sa majorité, l’établissement pourra refuser de servir la commande (Aucun remboursement ne sera effectué)<br>
<br>
Toute réduction, tout coupon ou code est limité à un seul usage par commande, et chaque réduction, coupon ou code ne peut être utilisé qu’une seule fois par personne.<br>
<br>
Aucune réduction, aucun coupon ou code ne peut être utilisé en conjonction avec une autre offre Praticboutic quelle qu’elle soit.<br>
<br>
Aucune réduction, aucun coupon ou code ne peut être échangé contre de l’argent ou toute autre alternative; ils n’ont en outre aucune valeur monétaire.<br>
L’annulation de toute commande entraînera l’invalidation du code utilisé sur le compte concerné. S’il s’agit d’une réduction ou un code réservée aux nouveaux clients, les nouveaux clients ne seront alors pas admissible ultérieurement à toute réduction, ou code réservé aux nouveaux clients.<br>
Toute réduction, tout bon ou code est utilisable uniquement pour une même commande, tout crédit restant après déduction de cette réduction, de ce coupon ou de ce code ne pourra pas être reporté sur des commandes supplémentaires ou ultérieures.<br>
<br>
Les réductions, bons ou codes pourraient être fournis sur la base d’un service clients et seront appliqués sur le compte client sous forme de crédit à utiliser en une seule commande.<br>
Toute tentative de manipulation du système et d’utilisation des réductions, bons ou codes par le biais de soumissions en vrac, de tiers ou de syndicats, de macros, d’un « script », de l’approche dite de « force brute», en masquant son identité grâce à une manipulation des adresses IP, en utilisant des identités autres que la sienne, ou tout autre moyen automatisé (y compris les systèmes programmables dans le but de générer des soumissions), rendra la commande et l’utilisation de ces réductions, bons ou codes invalide et pourrait potentiellement conduire à la clôture dudit compte, et ou à des poursuites judiciaire si une fraude avérée se voyait décelée.<br>
<br>
Si pour un motif quelconque, une réduction, un bon ou un code devient invalide à cause de problèmes techniques ou de toute autre raison échappant au contrôle de Praticboutic ou si un repas ou un établissement est indisponible, Praticboutic se réserve le droit (sous réserve de quelconques dispositions écrites  stipulées aux termes de la loi applicable) d’annuler, de suspendre ou de modifier la campagne liée à cette réduction, ce bonou ce code, sans réémettre de réduction, bon ou code supplémentaire aux clients affectés.<br>
Le cas échéant, Praticboutic se réserve le droit de prendre toutes les mesures qu’il considérera raisonnables pour se protéger contre les réclamations frauduleuses ou non valides, y compris, sans limitation, le droit d’exiger une vérification supplémentaire quant à l’identité du gagnant, son âge, et autres détails pertinents.<br>
<br>
En utilisant cette réduction, ce bon ou ce code, les clients acceptent de dégager Praticboutic de toute responsabilité en cas de réclamations, frais, blessures, pertes ou dommages de quelque nature qu’ils soient, résultant de cette campagne ou y étant liés ou résultant de l’acceptation ou de la possession de toute commande (sauf en cas de décès ou de blessures corporelles causés par la négligence du Promoteur, de fraude ou de toute action autrement interdite par la loi).<br>
<br>
Tous les Produits sont proposés sous réserve de disponibilité.<br>
L’inscription et les conditions générales standard de Praticboutic sont applicables – veuillez-vous y reporter sur cette page pour tout complément  d’information.<br>
<br>
Praticboutic utilisera les données personnelles fournies sur le compte du client et lors des commandes, uniquement à des fins de gestion des commandes et à aucune autre fin, sauf si nous avons obtenu votre consentement. Praticboutic se réserve le droit de divulguer les données personnelles du client à ses  prestataires, afin de permettre le bon déroulement d’une commande ou en réponse à une question de la part d’un client.<br><br>
</p>
  <?php
    echo '<input class="inpmove revenir" type="button" value="Revenir sur la commande" onclick="';
    echo 'window.location.href = \'getinfo.php?method=';
    echo $method;
    echo '&table=';
    echo $table;
    echo '&customer=';
    echo $customer;
    echo '\'"';
  ?>

  </body>
</html>

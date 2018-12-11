# PHP-MVC
Mini framework MVC compatible php 7.0 - MySql. 

Le moteur de template /ui:
- bootstrap4: surcouche permettant l'utilisation de bootstrap v4.1
  - renderer/Bootrap4Renderer.class.php: permet de mettre en forme les contrôles utilisateurs avec Bootstrap
  - CustomCheckbox.class.php: implémentation de la custom checkbox de Bootstrap
  - CustomRadio.class.php: implémentation de la custom radio de Bootstrap
  - FileInput.class.php: implémentation du tag input type="file" de Bootstrap
  - Input.class.php: implémentation du tag input générique de Bootstrap
- form: gestion des formulaires
  - Bindable.class.php: interface permettant de charger les attributs de l'objet qui l'implémente depuis un tableau associatif comme le $_GET ou le $_POST
- renderer: gestion de l'affichage des contrôles utilisateur
  - DefaultRenderer.class.php: permet de fournir un exemple d'utilisation
  - IRenderer.class.php: cette interface permet d'afficher les controles utilisateur de façon personalisée
- Checkbox.class.php: implémentation du tag input type="checkbox"
- Container.class.php: permet de créer un conteneur HTML comme une div ou une span
- Control.class.php: permet de créer des contrôles utilisateur
- HtmlElement.class.php: classe de base représentant un noeud HTML
- Input.class.php: implémentation du tag input générique
- InputNumber.class.php: implémentation du tag input type="number"
- InputRange.class.php: implémentation du tag input type="range"
- Label.class.php: implémentation du tag label
- Radio.class.php: implémentation du tag input type="radio"
- Select.class.php: implémentation du tag select
- TextArea.class.php: implémentation du tag textarea

Le moteur SQL:
- bdd: permet la génération des requêtes SQL
- dao: couche d'accès aux données

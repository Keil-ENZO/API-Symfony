## Start project
symfony server:start

Bonjour voici mon projet, mais je rencontre un problème avec le POST pour créer un utilisateur. Lorsque je tente de l’exécuter, la requête semble charger indéfiniment, et finit par retourner une réponse 500.

J’ai essayé de déboguer le problème, mais je n’ai pas encore réussi à identifier la cause.

Voici un exemple de User que j'ai creer avec une requete SQL:

```
{
  "email": "user@example.com",
  "password": "password"
}
```
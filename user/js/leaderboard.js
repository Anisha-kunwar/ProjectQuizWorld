const leaderboardData = [

{

rank:1,
name:"Anisha",
score:980,
quiz:20

},

{

rank:2,
name:"Major",
score:950,
quiz:18

},

{

rank:3,
name:"John",
score:920,
quiz:17

},

{

rank:4,
name:"Emma",
score:890,
quiz:15

},

{

rank:5,
name:"Alex",
score:860,
quiz:14

}

];



const tableBody = document.getElementById("leaderboardBody");


leaderboardData.forEach(player => {

const row = document.createElement("tr");`

<tr>

<td>${player.rank}</td>

<td>${player.name}</td>

<td>${player.score}</td>

<td>${player.quiz}</td>

</tr>

`;

});
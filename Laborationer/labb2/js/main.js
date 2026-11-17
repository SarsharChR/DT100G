"use strict";

var baseURL = "https://api.sr.se/api/v2/";
let audio;

// Vänta tills DOM är laddad
document.addEventListener("DOMContentLoaded", function () {
  //Hämta kanaler och bygg meny
  var url = baseURL + "channels?size=100&format=json";

  fetch(url)
    .then((response) => response.text())
    .then((data) => {
      var jsonData = JSON.parse(data);

      document.getElementById("mainnavlist").innerHTML = "";
      document.getElementById("searchProgram").innerHTML =
        "<option value='' selected disabled>Välj kanal...</option>";

      for (var i = 0; i < jsonData.channels.length; i++) {
        var ch = jsonData.channels[i];

        document.getElementById("mainnavlist").innerHTML += "<li id='" + ch.id + "'>" + "<img src='" + ch.image + "' alt='logo' style='width:1.5rem'> " + ch.name +"</li>";

        document.getElementById("searchProgram").innerHTML +=
          "<option value='" + ch.id + "'>" + ch.name + "</option>";

        // Stoppa listan vid P4 Jämtland
        if (ch.name === "P4 Jämtland") break;
      }
    })
    .catch((error) => {
      alert("There was an error " + error);
    });

  //knapp till Visa programtablå
  document
    .getElementById("searchbutton")
    .addEventListener("click", function (e) {
      e.preventDefault();

      var channelid = document.getElementById("searchProgram").value;
      if (!channelid) return;

      showSchedule(channelid);
    });

  //Klick i kanallistan
  document.getElementById("mainnavlist").addEventListener("click", function (e) {
    var channelid = e.target.id; // li har id = kanal-id
    if (!channelid) return;

    document.getElementById("searchProgram").value = channelid;
    showChannel(channelid);
  });
});

//Visa kanalinfo,spela stream och visa låtar
function showChannel(channelid) {
  // Skapa audio en gång
  if (!audio) audio = new Audio();

  // Spela kanalen
  audio.src =
    "https://sverigesradio.se/topsy/direkt/srapi/" + channelid + "-hi.mp3";
  audio.play().catch(function () {});

  // Hämta kanalinfo
  fetch(baseURL + "channels/" + channelid + "?format=json")
    .then((resp) => resp.text())
    .then((text) => {
      var data = JSON.parse(text);

      var name = data.channel.name;
      var tagline = data.channel.tagline || "";

      document.getElementById("info").innerHTML ="<h2>" + name + "</h2>" + "<p><strong>" + tagline + "</strong></p>" + "<p>Previous Song: <span id='prevSong'>Loading...</span></p>" +"<p>Next Song: <span id='nextSong'>Loading...</span></p>";

      // Hämta playlist (låtar just nu)
      fetch(
        baseURL +
          "playlists/rightnow?channelid=" +
          channelid +
          "&format=json"
      )
        .then((r) => r.text())
        .then((t) => {
          var p = JSON.parse(t);

          var prevText = "N/A by N/A";
          var nextText = "N/A by N/A";

          // Vissa svar har playlist.song, andra har previoussong/nextsong
          if (p.playlist) {
            if (p.playlist.song) {
              var songs = p.playlist.song;
              if (!Array.isArray(songs)) songs = [songs];
              if (songs[0]) prevText = songText(songs[0]);
              if (songs[1]) nextText = songText(songs[1]);
            } else {
              if (p.playlist.previoussong) prevText = songText(p.playlist.previoussong);
              if (p.playlist.nextsong) nextText = songText(p.playlist.nextsong);
            }
          }

          document.getElementById("prevSong").textContent = prevText;
          document.getElementById("nextSong").textContent = nextText;
        });
    });
}

// Gör om en låt till text
function songText(s) {
  var title = s.title || s.name || "N/A";
  var artist = s.artist || s.artistname || "N/A";
  return title + " by " + artist;
}

// Visa programtablå 
function showSchedule(channelid) {
  var url =
    baseURL +
    "scheduledepisodes?channelid=" +
    channelid +
    "&format=json&size=200";

  fetch(url)
    .then((resp) => resp.text())
    .then((text) => {
      var data = JSON.parse(text);
      var episodes = data.schedule || data.scheduledepisodes || [];

      document.getElementById("info").innerHTML = "";

      
      for (var i = 0; i < Math.min(40, episodes.length); i++) {
        var ep = episodes[i];

        var title = ep.title || (ep.program && ep.program.name) || "";
        var desc =
          ep.description || (ep.program && ep.program.description) || "";
        var start = parseDate(ep.starttimeutc);

        document.getElementById("info").innerHTML += "<article>" +"<h2>" + title +"</h2>" +"<h3>" +desc +"</h3>" +"<p>" +start +"</p>" +"<hr>" +"</article>";
      }
    });
}

// Konvertera SR datumsträng (/Date(...)/) till vanlig datum/tid
function parseDate(srDate) {
  if (!srDate) return "";
  var m = String(srDate).match(/\d+/);
  if (!m) return "";
  return new Date(parseInt(m[0], 10)).toLocaleString("sv-SE");
}
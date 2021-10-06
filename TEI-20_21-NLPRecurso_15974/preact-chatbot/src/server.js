require('dotenv').config({ path: 'variables.env' });

const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const { Wit } = require('node-wit');
const Pusher = require('pusher');

const pusher = new Pusher({
  appId: process.env.PUSHER_APP_ID,
  key: process.env.PUSHER_APP_KEY,
  secret: process.env.PUSHER_APP_SECRET,
  cluster: process.env.PUSHER_APP_CLUSTER,
  useTLS: true,
});

const client = new Wit({
  accessToken: process.env.WIT_ACCESS_TOKEN,
});

const app = express();

app.use(cors());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());

const vehicleList = [
  {
    id: 1,
    name: "limousine",
    priceKm: 20
  },
  {
    id: 2,
    name: "7 seater",
    priceKm: 4
  },
  {
    id: 3,
    name: "executive",
    priceKm: 10
  },
  {
    id: 4,
    name: "city",
    priceKm: 0.50
  }
]

app.post('/chat', (req, res) => {
  const { message } = req.body;
  const { chatRoomID } = req.body;

  const responses = {
    greetings: [
      "Hey, how's it going?", 
      "Hi", 
      "What's up"
    ],

    kenobi: ["General Kenobi!"],

    jokes: [
      'Do I lose when the police officer says papers and I say scissors?',
      'I have a clean conscience. I haven’t used it once until now.',
      'Did you hear about the crook who stole a calendar? He got twelve months.',
    ],
    sayBye: [
      "Goodbye",
      "See you later",
      "Have a nice day",
    ],
    gratitude: [
      "You're welcome :D",
      "You're welcome!",
      "No problem, glad to help",
      "Don't sweet, I'm really glad to have helped!"
    ]
  };

  const firstEntityValue = (entities, entity) => {
    const val =
      entities &&
      entities[entity] &&
      Array.isArray(entities[entity]) &&
      entities[entity].length > 0 &&
      entities[entity][0].value;



    if (!val) {
      return null;
    }
    return val;
  };

  const firstEntityValueLocation = (entities, entity) => {
    const val =
      entities &&
      entities[entity] &&
      Array.isArray(entities[entity]) &&
      entities[entity].length > 0 &&
      entities[entity][0].body;



    if (!val) {
      return null;
    }
    return val;
  };

  const handleMessage = ({ entities }) => {
    const greetings = firstEntityValue(entities, 'greet:greet');
    const goodBye = firstEntityValue(entities, 'sayBye:sayBye');
    const gratitude = firstEntityValue(entities, "isGrateful:isGrateful");
    const kenobi = firstEntityValue(entities, 'kenobi_bot:kenobi_bot');
    const jokes = firstEntityValue(entities, 'jokes:jokes');
    const getCostDistance = firstEntityValue(entities, 'getCostDistance:getCostDistance');
    const getPrices = firstEntityValue(entities, 'getPrices:getPrices');
    const getVehicles = firstEntityValue(entities, 'getVehicles:getVehicles');
    const scheduleTrip = firstEntityValue(entities, 'scheduleTrip:scheduleTrip');
    const specificVehicle = firstEntityValue(entities, "getSpecificVehicle:getSpecificVehicle");
    const dateTime = firstEntityValue(entities, 'wit$datetime:datetime');
    const distance = firstEntityValue(entities, 'wit$distance:distance');
    const location = firstEntityValueLocation(entities, 'wit$location:location');

    if (gratitude) {
      return pusher.trigger('bot' + chatRoomID, 'bot-response', {
        message:
          responses.gratitude[
          Math.floor(Math.random() * responses.gratitude.length)
          ],
      });
    }

    if (goodBye) {
      return pusher.trigger('bot' + chatRoomID, 'bot-response', {
        message:
          responses.sayBye[
          Math.floor(Math.random() * responses.sayBye.length)
          ],
      });
    }

    if (scheduleTrip) {
      const distanceGiven = entities["wit$distance:distance"][0].value
      const carNameGiven = entities["getSpecificVehicle:getSpecificVehicle"][0].value;
      const dateTimeChosen = entities['wit$datetime:datetime'][0].value;
      const locationGiven = entities['wit$location:location'][0].body;
      var foundCarID = 0;
      var foundCarPriceKm = 0;
      if (location) {
        if (distance) {
          if (specificVehicle) {
            vehicleList.map(function (v) {
              if (carNameGiven.toLowerCase() === v.name) {
                foundCarID = v.id
              }
            })
            if (foundCarID === 0) {

            } else {
              vehicleList.map(function (v) {
                if (foundCarID === v.id) {
                  foundCarPriceKm = v.priceKm
                }
              })
            }

            const priceOfTrip = distanceGiven * foundCarPriceKm;
            if (dateTime) {
              return pusher.trigger('bot' + chatRoomID, 'bot-response', {
                message: 'Your trip to ' + locationGiven + ' has been scheduled for ' + dateTimeChosen + ' and the price of the trip will be ' + priceOfTrip + "€.\nIs there anything else you need?",
              });
            } else {
              return pusher.trigger('bot' + chatRoomID, 'bot-response', {
                message: 'I\'m sorry, but i need to know what time and date you want.\nPlease ask me again, but give me the time and date please.',
              });
            }
          } else {
            return pusher.trigger('bot' + chatRoomID, 'bot-response', {
              message: 'I\'m sorry, but i need to know which vehicle to use in the calculations.\nI recommend the city vehicle, as it is the cheapest. Please ask again with that information.',
            });
          }
        } else {
          return pusher.trigger('bot' + chatRoomID, 'bot-response', {
            message: 'I\'m sorry, but i need to know what distance to use in the calculations.\nPlease ask me again, but with that information.',
          });
        }
      } else {
        return pusher.trigger('bot' + chatRoomID, 'bot-response', {
          message: 'I\'m sorry, but i need to know where you\'re going.\nPlease ask me again, but with all the information required.',
        });
      }
    }

    if (getVehicles) {
      return pusher.trigger('bot' + chatRoomID, 'bot-response', {
        message: 'The vehicles we currently have are ' + vehicleList.map(function (v) {
          return v.name;
        }).join(", "),
      });
    }

    if (getCostDistance) {
      const distanceGiven = entities["wit$distance:distance"][0].value
      const carNameGiven = entities["getSpecificVehicle:getSpecificVehicle"][0].value;
      var foundCarID = 0;
      var foundCarPriceKm = 0;
      if (distance) {
        if (specificVehicle) {
          vehicleList.map(function (v) {
            if (carNameGiven.toLowerCase() === v.name) {
              foundCarID = v.id
            }
          })
          if (foundCarID === 0) {

          } else {
            vehicleList.map(function (v) {
              if (foundCarID === v.id) {
                foundCarPriceKm = v.priceKm
              }
            })
          }

          const priceOfTrip = distanceGiven * foundCarPriceKm;
          return pusher.trigger('bot' + chatRoomID, 'bot-response', {
            message: 'The price of the trip will be ' + priceOfTrip + "€.\n If you want to schedule it, just ask me again to schedule, with all the information you provided, plus the location\nBoth the location and distance are required in this version! :)",
          });
        } else {
          return pusher.trigger('bot' + chatRoomID, 'bot-response', {
            message: 'I\'m sorry, but i need to know which vehicle to use in the calculations.\nI recommend the city vehicle, as it is the cheapest. Please ask again with that information.',
          });
        }
      } else {
        return pusher.trigger('bot' + chatRoomID, 'bot-response', {
          message: 'I\'m sorry, but i need to know what distance to use in the calculations.\nPlease ask me again, but with that information.',
        });
      }
    }



    if (getPrices) {
      return pusher.trigger('bot' + chatRoomID, 'bot-response', {
        message: 'For the vehicles we currently have, ' + vehicleList.map(function (v) {
          return v.name;
        }).join(", ") + " the prices per Km are as follows, by order: " + vehicleList.map(function (v) {
          return v.priceKm;
        }).join(", "),
      });
    }

    if (kenobi) {
      return pusher.trigger('bot' + chatRoomID, 'bot-response', {
        message:
          responses.kenobi[
          Math.floor(Math.random() * responses.kenobi.length)
          ],
      });
    }

    if (greetings) {
      return pusher.trigger('bot' + chatRoomID, 'bot-response', {
        message:
          responses.greetings[
          Math.floor(Math.random() * responses.greetings.length)
          ],
      });
    }

    if (jokes) {
      return pusher.trigger('bot' + chatRoomID, 'bot-response', {
        message:
          responses.jokes[
          Math.floor(Math.random() * responses.jokes.length)
          ],
      });
    }

    return pusher.trigger('bot' + chatRoomID, 'bot-response', {
      message: 'I don\'t know that yet, but I can schedule a trip for you, tell you about our prices, and what vehicles we have.\nI can give you the cost of a trip if you give us the origin address, destination address and the type of vehicle.',
    });
  };

  client
    .message(message)
    .then(data => {
      handleMessage(data);
    })
    .catch(error => console.log(error));
});

app.set('port', process.env.PORT || 7777);
const server = app.listen(app.get('port'), () => {
  console.log(`Express running → PORT ${server.address().port}`);
});
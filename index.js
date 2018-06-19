const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');

const app = express();

app.use(cors());
app.use(bodyParser.json());

const ROOMS = [
    {
		id: 1,
		room: "Test PENA DE MUERTE 1962",
		description:
			"Se acerca la última cena de un hombre a punto de ser sometido a pena de muerte. Sois un grupo de activistas que vais a entrar en contacto con el presunto asesino de Fred Shoz. Vuestra misión será encontrar indicios suficientes para reabrir el caso y detener la ejecución antes de que sea demasiado tarde. Rápido, vuestro tiempo se acaba…",
		players: {
			min: 3,
			max: 5
		},
		level: 1,
		duration: 60,
        pricing: 15,
        favorite: false,
        recomended: false,
	},
    {
		id: 2,
		room: "test UN CASO PERDIDO",
		description:
        "Un profesor de periodismo desaparecido... Un mayordomo que pide ayuda... Un hombre oscuro como el chocolate Valor... Tu equipo de investigadores se pone en marcha. Con tan solo 60 minutos para lograr vuestros objetivos, quién sabe lo que encontraréis al llegar…\n¿Misterio? ¿Traición? ¿...o una simple sala de escape?",
		players: {
			min: 2,
			max: 6
		},
		level: 1,
		duration: 60,
        pricing: 9,
        favorite: true,
        recomended: false,
    },
    {
		id: 3,
		room: "Test EL MARCHANTE DE ARTE 1932",
		description:
        "Te encuentras en Madrid, en la casa del Sr.Byne, un comerciante de arte sospechoso. Han desaparecido obras de arte importantes para el patrimonio histórico español. Su barco a Nueva York sale de Santander en poco más de una hora. ¿Podrás encontrar la prueba de sus negocios ilícitos o, por el contrario, demostrarás que Byne es inocente?",
		players: {
			min: 2,
			max: 5
		},
		level: 1,
		duration: 60,
        pricing: 15,
        favorite: false,
        recomended: false,
    },
    {
		id: 4,		
		room: "Test GUERRA FRIA - AGENTE DOBLE 1976",
		description:
			"El mundo está en alerta, las tensiones entre los países van en aumento, los sistemas de seguridad son altamente vulnerables y cualquier mente perversa puede desatar un conflicto nuclear. Forma parte de un comando de la CIA. ¡Solo tienes 66 minutos para detener la tercera guerra mundial!",
		players: {
			min: 2,
			max: 5
		},
		level: 2,
		duration: 66,
        pricing: 15,
        favorite: false,
        recomended: false
	},
        
];

const createError = (status, code, message) => {
  const error = new Error(message);
  error.code = code;
  error.status = status;

  return error;
};

app.get('/rooms', (req,res) => {
    const { favorites, recomended, error} = req.query
    let rooms = ROOMS
    rooms = filterBy(rooms, favorites, 'favorite')
    rooms = filterBy(rooms, recomended, 'recomended')
    if(error) es.status(400).json({ code: 'Error' });
    setTimeout((() =>  {res.json(rooms);}), 3000);
});

const validators = ['true',true,1]
const isFilterValid = (filterName) => validators.indexOf(filterName) >= 0 ? true : false
const filterBy = (items, filterValue, filter) => isFilterValid(filterValue) ? items.filter(item => item[filter] === true) : items

app.use((err, req, res, next) => {
  if (err.code && err.status) {
    res.status(err.status).json({
      code: err.code,
      message: err.message
    });
  } else {
    res.status(500).json({ code: 'FATAL' });
  }
});

const port = process.env.PORT || 4000;
app.listen(port, () => {
  console.log(`Listening on port ${port}`);
});

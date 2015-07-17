console.time('load');

console.log('child loaded');

var domain = '*';

function parentMessage(data) {
	parent.postMessage(data, domain);
}

document.addEventListener('DOMContentLoaded', function(event) {
  parentMessage('load');
});


function receiveMessage(e){
	//if(e.origin!=='http://localhost:8888')
	//return;
	//console.log(e.data);
	interpretFunction(e);
}

function interpretFunction(e) {
	try {

		console.log('function interpreted');

		eval('(' + decodeURI(e.data) + ')();');
	} catch(e) {}
}


window.addEventListener('message',receiveMessage);

console.timeEnd('load');
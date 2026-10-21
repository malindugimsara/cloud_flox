const { spawn } = require('child_process');
const http = require('http');
const fs = require('fs');

async function test() {
    const chrome = spawn('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', [
        '--headless=new',
        '--remote-debugging-port=9241',
        '--user-data-dir=C:\\xampp\\htdocs\\cf-website\\scratch\\temp_prof9241',
        '--window-size=1920,1080',
        'http://localhost:8080/cf-website/?v=41'
    ]);
    await new Promise(r => setTimeout(r, 2500));
    http.get('http://127.0.0.1:9241/json', res => {
        let b = ''; res.on('data', d => b += d);
        res.on('end', () => {
            const list = JSON.parse(b);
            const p = list.find(x => x.type === 'page');
            const ws = new WebSocket(p.webSocketDebuggerUrl);
            ws.onopen = () => {
                ws.send(JSON.stringify({
                    id: 1,
                    method: 'Runtime.evaluate',
                    params: {
                        expression: `
                            const p = document.getElementById('offerPopup');
                            if (p) p.remove();
                            const pr = document.querySelector('.preloader');
                            if (pr) pr.remove();
                            const sec = document.getElementById('pricingSection');
                            sec.scrollIntoView();
                            sec.getBoundingClientRect().width;
                        `,
                        returnByValue: true
                    }
                }));
            };
            ws.onmessage = msg => {
                const d = JSON.parse(msg.data);
                if (d.id === 1) {
                    console.log('Section width:', d.result.result.value);
                    setTimeout(() => {
                        ws.send(JSON.stringify({ id: 2, method: 'Page.captureScreenshot', params: { format: 'png' } }));
                    }, 500);
                } else if (d.id === 2) {
                    fs.writeFileSync('C:\\Users\\MG\\.gemini\\antigravity-ide\\brain\\e22aa201-de3b-4ba3-9521-b10d80b6fd87\\.tempmediaStorage\\pricing_fullwidth_24inch.png', Buffer.from(d.result.data, 'base64'));
                    console.log('Saved pricing_fullwidth_24inch.png successfully!');
                    ws.close();
                    chrome.kill();
                    process.exit(0);
                }
            };
        });
    });
}
test();

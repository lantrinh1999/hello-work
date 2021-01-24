const puppeteer = require('puppeteer');
const request = require('request');

const API = 'http://127.0.0.1:9999/api/insert';

let electronicUrl = 'https://www.hellowork.mhlw.go.jp/kensaku/GECA110010.do?action=initDisp&screenId=GECA110010';

let count = 0;

(async () => {
    const browser = await puppeteer.launch({
        headless: true
    });
    const page = await browser.newPage();

    await page.goto(electronicUrl, {
        waitUntil: 'load'
    });

    await page.select('#ID_tDFK1CmbBox', '03')

    await Promise.all([
        page.click("input#ID_searchBtn"),
        page.waitForNavigation({
            waitUntil: 'networkidle0'
        }),
    ]);

    let json_jobs = await page.evaluate(() => {
        let jobs = [];
        let job_wrapper = document.querySelectorAll('#ID_dispDetailBtn');
        if (job_wrapper.length < 1) {
            return;
        }
        job_wrapper.forEach((job) => {
            let dataJson = {};
            try {
                dataJson.link = job.href;
            } catch (err) {
                console.log(err);
            }
            jobs.push(dataJson);
        });

        return jobs;

    });

    console.log(json_jobs.length);
    count += parseInt(json_jobs.length)
    if (json_jobs.length < 1) {
        return;
    }
    // api here
    request.post(API, { json: { json: JSON.stringify(json_jobs) } });


    for (let index = 0; index < 999999999999999; index++) {
        await Promise.all([
            page.click("input[name='fwListNaviBtnNext']"),
            page.waitForNavigation({
                waitUntil: 'networkidle0'
            }),
        ]);
        let json_jobs = await page.evaluate(() => {
            let jobs = [];
            let job_wrapper = document.querySelectorAll('#ID_dispDetailBtn');
            if (job_wrapper.length < 1) {
                return;
            }
            job_wrapper.forEach((job) => {
                let dataJson = {};
                try {
                    dataJson.link = job.href;
                } catch (err) {
                    console.log(err);
                }
                jobs.push(dataJson);
            });

            return jobs;

        });
        count += parseInt(json_jobs.length)
        console.log(count);
        if (json_jobs.length < 1) {
            return;
        }
        // api
        request.post(API, { json: { json: JSON.stringify(json_jobs) } });

        console.log('Waiting two seconds...');
        await sleep(2000);
    }

    await browser.close();
})();

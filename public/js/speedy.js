export default class Speedy {
    currentVersion = 'v0.0.1'
    availableFrames;

    constructor() {
        customElements.define('speedy-frame', SpeedyFrame, {extends: 'div'})
        this.historyListener();
        this.preventNavigation();
        this.updateAvailableFrames();
    }

    updateAvailableFrames() {
        this.availableFrames = [...document.querySelectorAll('speedy-frame')];
    }

    getFrame(id) {
        return this.availableFrames.find((element) => element.id !== null && element.id === id);
    }

    findFrames(doc) {
        return doc.querySelectorAll('speedy-frame[id]');
    }

    loadPage(link) {
        fetch(link, {
            method: 'GET',
            headers: {
                'Speedy': this.currentVersion
            }
        }).then(response => {
                if (response.headers.get('Speedy') !== this.currentVersion) {
                    location.reload();
                    return;
                }
                if (response.headers.get('Title') !== null) {
                    document.title = response.headers.get('Title');
                }
                response
                    .text()
                    .then(body => {
                            let doc = new DOMParser().parseFromString(body, 'text/html');
                            let frames = this.findFrames(doc);
                            frames.forEach((frame) => {
                                if (frame.id !== undefined && this.getFrame(frame.id) !== undefined) {
                                    this.getFrame(frame.id).innerHTML = frame.innerHTML;
                                    this.preventNavigation();
                                } else {
                                    location.reload();
                                }
                            });
                        }
                    );
            }
        ).catch(e => console.debug(e));
    }

    preventNavigation() {
        document
            .querySelectorAll('a')
            .forEach((a) => {
                if (a.speedyListener === true) return;
                a.speedyListener = true;
                a.addEventListener('click', (evt) => {
                    evt.preventDefault();
                    this.loadPage(a.href);
                    history.pushState(null, null, a.href);
                })
            });
    }

    historyListener() {
        addEventListener('popstate', (evt) => {
            this.loadPage(location.pathname);
            history.pushState(null, null, location.pathname);
        }, false);
    }
}


class SpeedyFrame extends HTMLParagraphElement {
    constructor() {
        super();
    }
}
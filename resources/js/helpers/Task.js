export class Task {
    constructor() {
        this.id = 0
        this.infinite = false
        this.for_type = ''
        this.for_id = ''
        this.not_before = new Date()
        this.name = ''
        this.payload = {}
    }

    for(id, name) {
        this.for_id = id
        this.for_type = name
        return this
    }

    notBefore(date) {
        this.not_before = date
    }

    asInfinite() {
        this.infinite = true
        return this
    }

    asAddObject(path) {
        this.name = 'AddObject'
        this.payload = {
            parameter: path
        }
    }

    asDeleteObjectTask(path) {
        this.name = 'DeleteObject'
        this.payload = {
            parameter: path
        }
    }

    asScriptTask(script) {
        this.name = 'RunScript'
        this.payload = {
            script: script
        }
    }

    asFirmwareUpdateTask(filetype, filename) {
        this.name = 'UploadFirmware'
        this.payload = {
            filetype: filetype,
            filename: filename,
        }
    }

    asReboot() {
        this.name = 'Reboot'
        this.payload = {}
    }
}

export function taskFromObject(obj) {
    let task = new Task()
    task = Object.assign(task, obj)
    return task
}
export class FlagParser {
  constructor(flag_obj) {
    this.flag = flag_obj
  }

  toString() {
    let ret = ""
    Object.keys(this.flag).forEach((item) => {
      if(this.flag[item] === true) {
        ret += `${item} `
      }
    })

    return ret
  }
}

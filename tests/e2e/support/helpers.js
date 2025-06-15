module.exports = {
  getDateTimeString: ($date) => {
    return $date.toISOString().slice(0, 19).replace('T',' ');
  },
  getDateString: ($date) => {
    return $date.toISOString().slice(0, 10);
  }
}

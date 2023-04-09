function secondToTimeStr(t) {
    if (!t) return;
    if (t < 60) return "00:" + ((i = t) < 10 ? "0" + i : i);
    if (t < 3600) return "" + ((a = parseInt(t / 60)) < 10 ? "0" + a : a) + ":" + ((i = t % 60) < 10 ? "0" + i : i);
    if (3600 <= t) {
        var a, i, e = parseInt(t / 3600);
        return (e < 10 ? "0" + e : e) + ":" + ((a = parseInt(t % 3600 / 60)) < 10 ? "0" + a : a) + ":" + ((i = t % 60) < 10 ? "0" + i : i);
    }
}
var str = secondToTimeStr(6000);
console.log(str);
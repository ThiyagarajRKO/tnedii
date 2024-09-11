function convert() {
    var e = document.getElementById("input").value;
    try {
        var r = (convertSQL(e) + "\n->get();").trim();
        r = markUp(r);
        var t = "get</span><span style='color:gray'>(</span><span style='color:gray'>)</span><span style='color:gray'>;</span>";
        document.getElementById("result").innerHTML = r.split(t)[0] + t;
        console.log(r.split(t)[0] + t);
    } catch (e) {
        console.log(e.message),
            (document.getElementById("result").innerHTML =
                "Cannot parse your SQL Statement. Please check your syntax. \nPlease note, only SELECT statements are considered valid syntax.\n\nRules: \n1. Use parentheses when using BETWEEN operator. \n\te.g. \n\tSELECT * FROM t WHERE (column_name BETWEEN value1 AND value2);\n2. When using ALIAS, always use the AS linking verb. \n\te.g. \n\tSELECT uid AS `user_id`;\n3. Always use backticks (`) for aliases.");
    }
}
function getEnders(e) {
    var r = [],
        t = /(limit (\d+), (\d+)|limit (\d+) offset (\d+)|limit (\d+))/gi,
        n = getAll(t, (e = e.toLowerCase().trim())),
        i = n.matches;
    return (
        i.length > 0 && (r.push(getLimit(i[0].replace(/limit/g, "").trim())), (e = n.input)),
        (i = (n = getAll((t = /(order by (.+) ((asc|desc))?)/gi), e)).matches).length > 0 &&
            (r.push(
                `->orderBy(${i[0]
                    .replace(/order by/g, "")
                    .trim()
                    .split(" ")
                    .map(function (e) {
                        return `"${e}"`;
                    })
                    .join(",")})`
            ),
            (e = n.input)),
        (i = (n = getAll((t = /((group by (\w\.\w+))|(group by (\w+)))/gi), e)).matches).length > 0 &&
            (r.push(
                `->groupBy(${i[0]
                    .replace(/group by/g, "")
                    .trim()
                    .split(" ")
                    .map(function (e) {
                        return `"${e}"`;
                    })
                    .join(",")})`
            ),
            (e = n.input)),
        { input: e, strings: r }
    );
}
function getLimit(e) {
    return (
        (string = ""),
        /offset|,/g.test(e)
            ? ((parts = e.split(/offset|,/g)),
              /offset/g.test(e)
                  ? (void 0 !== parts[1] && (string += `->offset(${(parts[1] || "").trim()})`), (string += `->limit(${parts[0].trim()})`))
                  : ((string += `->offset(${parts[0].trim()})`), void 0 !== parts[1] && (string += `->limit(${(parts[1] || "").trim()})`)))
            : (string += `->limit(${e})`),
        string
    );
}
function convertSQL(e, r = !1) {
    console.log(e.toLowerCase().includes("select"),e.toLowerCase().includes("from"));
    if (!e.toLowerCase().includes("select") || !e.toLowerCase().includes("from")) throw "Syntax Error";
    var t = (e = (e = (e = (e = (e = (e = (e = (e = (e = (e = (e = e.toLowerCase().trim()).replace(/;/g, "")).replace(/"/g, "'")).replace(/=/g, " = ")).replace(/< =/g, " <= ")).replace(/> =/g, " >= ")).replace(/! =/g, " != ")).replace(
            /,/g,
            ", "
        )).replace(/in\(/g, "in (")).replace(/(\r\n|\n|\r)/gm, " ")).replace(/\s+/gm, " ")).split(/ union /g),
        n = "",
        i = "";
    void 0 !== t[1] && ((n = "$table = " + convertSQL(t[1]) + ";\n\n"), (i = "\n->union($table)")), (e = t[0]), r && (e = e.trim().replace(/^\(/g, "").replace(/\)$/g, ""));
    var o = getAll(/case when (.+?) end (.+?)`(.+?)`/g, e);
    (select_raws = o.matches),
        (e = (o = getAll(/(([a-z]|[a-z]_[a-z])+?| )\(.+?\)( | as)`.+?`/g, (e = o.input), "select_subquery_function")).input),
        (select_subqueries_functions = o.result),
        (e = (o = getAll(/( *?\(.+?\))/g, e, "where_subquery_group")).input),
        (where_subqueries_groups = o.result);
    var s = getEnders((e = e.replace(/\s+/gm, " "))),
        a = (e = s.input).split(/select | from | where | order by | limit /),
        l = { select: a[1] || "", from: a[2] || "", where: a[3] || "", order_by: a[4] || "", limit: a[5] || "" };
    return (
        (output_string = compose(l, select_raws, select_subqueries_functions, where_subqueries_groups, r)),
        (semicolon = ""),
        r && (semicolon = ";"),
        (delimiter = ""),
        s.strings.length > 0 && (delimiter = "\n"),
        `${n}${output_string}${delimiter}${i}${s.strings.join("\n")}${semicolon}`
    );
}
function compose(e, r, t, n, i) {
    var o,
        s = n,
        a = [],
        l = "\n",
        c = e.from.split(/left join|right join|inner join|full join|cross join|join/);
    (o = c[0].trim()), i ? (a.push(`$query->from("${o}")`), (l = "\n\t")) : a.push(`DB::table("${o}")`);
    var u = 0;
    for (table_clause of c) 0 != u ? (a.push(join(table_clause, e.from.trim().match(/left join|right join|inner join|full join|cross join|join/g), u - 1, s)), u++) : u++;
    var p = e.select
        .split(",")
        .filter((e) => !e.trim().includes("select_subquery_function") && "" != e.trim())
        .map(function (e) {
            return `"${e.trim()}"`;
        })
        .join(", ");
    for (column of ('"*"' != p && a.push(`->select(${changeGroups(p, s)})`), r)) (column = column.trim()), a.push(`->addSelect(DB::raw("${column}"))`);
    for (column of e.select.split(","))
        (column = column.trim()),
            column.includes("select_subquery_function") &&
                ((value = t[` ${column}`] || ""),
                (value = value.trim()),
                "" != value && (/^\(.+?/g.test(value) ? ((alias = getAlias(value)), "" != alias && a.push(`->addSelect(["${alias}" => ${getSubquery(value)}])`)) : a.push(`->addSelect(DB::raw("${value}"))`)));
    if ("" != e.where.trim())
        for (condition of ((first_condition = e.where.split(/ and | or /)[0].trim()),
        "" != first_condition && a.push(where(first_condition, s)),
        (get_all = getAll(/ and | or /g, e.where.replace(first_condition, "").trim())),
        (operators = get_all.matches),
        (conditions = e.where
            .trim()
            .split(/ and | or /g)
            .map(function (e) {
                return e.trim();
            })),
        (u = 0),
        conditions))
            if (0 != u) {
                pre = "or" == (operators[u - 1] || "") ? "orWhere" : "where";
                try {
                    a.push(where(condition, s, pre));
                } catch (e) {
                    console.log(u, conditions);
                }
                u++;
            } else u++;
    return a.join(l);
}
function changeGroups(e, r) {
    var t = /where_subquery_group_(\d+)_/g;
    if (t.test(e) && ((matches = e.match(t)), Array.isArray(matches))) for (match of matches) void 0 !== r[` ${match}`] && (e = e.replace(match, r[` ${match}`].trim()).trim());
    return e;
}
function join(e, r, t, n) {
    if (
        (Array.isArray(r) || (r = []),
        void 0 !==
            (r = r.map(function (e) {
                return e.trim();
            }))[t])
    ) {
        var i = r[t].replace(/^(.)|\s+(.)/g, function (e) {
            return e.toUpperCase();
        });
        i = lowerCaseFirstLetter((i = i.replace(/ /g, "")));
        var o = e.split(/on/g)[0].trim();
        if ("crossJoin" == i) {
            if (((regex = /where_subquery_group_(\d+)/g), regex.test(e) && ((matches = e.match(regex)), Array.isArray(matches)))) for (match of matches) void 0 !== n[` ${match}`] && (e = e.replace(match, n[` ${match}`].trim()).trim());
            return `->${i}(DB::raw("${e}"))`;
        }
        return `->${i}("${o}", function($join){\n\t${joinCondition(e.replace(o, "").trim(), n)}\n})`;
    }
    return "";
}
function joinCondition(e, r) {
    console.log(e);
    var t = [],
        n = (e = e.replace(/on /g, "")).split(/ and | or /g)[0].trim();
    "" != n && t.push(conditionOn(n, r));
    var i = getAll(/ and | or /g, e.replace(n, "").trim());
    operators = i.matches;
    var o = e
        .trim()
        .split(/ and | or /g)
        .map(function (e) {
            return e.trim();
        });
    for (condition of ((x = 0), o))
        if (0 != x) {
            pre = "or" == (operators[x - 1] || "") ? "orWhere" : "where";
            try {
                t.push(where(condition, r, pre));
            } catch (e) {
                console.log(pre, condition), t.push(`->${pre}(DB::raw("${condition}"))`);
            }
            x++;
        } else x++;
    return "$join" + t.join("\n\t") + ";";
}
function conditionOn(e, r, t = "on") {
    var n = e.split(" ");
    return void 0 === n[1] && void 0 !== r[` ${n[0].trim()}`]
        ? `->${t}(DB::raw("${r[` ${n[0]}`].trim()}"))`
        : void 0 === n[1]
        ? `->${t}(DB::raw("${e.trim()}"))`
        : ((last = `"${n[2].trim()}"`),
          ((n[2].trim().startsWith("'") && n[2].trim().endsWith("'")) || (n[2].trim().startsWith('"') && n[2].trim().endsWith('"'))) && (last = n[2].trim()),
          e.includes("where_subquery_group") &&
              ((condition = r[` ${n[2].trim()}`].trim()), /^(\(select|\( select)/g.test(condition) ? (last = getSubquery(condition)) : (last = "[" + r[` ${n[2]}`].trim().replace(/^\(/g, "").replace(/\)$/g, "") + "]")),
          "in" == n[1].trim() ? `->${t}In("${n[0]}", ${last})` : "is" == n[1].trim() || "between" == n[1].trim() ? `->${t}(DB::raw("${e}"))` : `->${t}("${n[0]}", "${n[1]}", ${last})`);
}
function lowerCaseFirstLetter(e) {
    return e.charAt(0).toLowerCase() + e.slice(1);
}
function where(e, r, t = "where") {
    console.log(t, e);
    var n = e.split(" "),
        i = e,
        o = `"${i.split(" ")[0] || ""}", "${i.split(" ")[1] || ""}"`;
    return void 0 === n[1] && void 0 !== r[` ${n[0].trim()}`]
        ? ((grouped_value = r[` ${n[0]}`].trim()),
          /between /g.test(grouped_value) && grouped_value.split(" ").length < 6
              ? ((grouped_value = grouped_value.replace(/\(/g, "")),
                (grouped_value = grouped_value.replace(/\)/g, "")),
                (grouped_parts = grouped_value.split(" ").map(function (e) {
                    return e.trim();
                })),
                `->${t}Between("${grouped_parts[0]}", [${grouped_parts[2] || ""}, ${grouped_parts[4] || ""}])`)
              : `->${t}(DB::raw("${grouped_value}"))`)
        : void 0 === n[1]
        ? (console.log(n), `->${t}(DB::raw("${e.trim()}"))`)
        : ((last = `"${n[2]}"`),
          e.includes("where_subquery_group") &&
              ((condition = r[` ${n[2].trim()}`].trim()), /^(\(select|\( select)/g.test(condition) ? (last = getSubquery(condition)) : (last = "[" + r[` ${n[2]}`].trim().replace(/^\(/g, "").replace(/\)$/g, "") + "]")),
          "in" == n[1].trim()
              ? `->${t}In("${n[0]}", ${last})`
              : /is null/g.test(e)
              ? `->${t}Null("${n[0]}")`
              : /is not null/g.test(e)
              ? `->${t}NotNull("${n[0]}")`
              : `->${t}(${o}, ${last.trim().replace(/^"|"$/g, "").replace(/^'|'$/g, "")})`);
}
function getSubquery(e) {
    return `function($query){\n\t${convertSQL((e = e.replace(/`(.+?)`$/g, "")), !0)}\n}`;
}
function getAlias(e) {
    var r = (e = e.trim()).match(/`.+?`$/g);
    return Array.isArray(r) || (r = []), (r[0] || "").replace(/`/g, "").trim();
}
function getAll(e, r, t = "") {
    var n = {},
        i = r.match(e);
    Array.isArray(i) || (i = []);
    var o = 1;
    for (match of i) (replace = ` ${t}_${o}_`), "" == t && (replace = ""), (r = r.replace(match, replace)), (n[replace] = match), o++;
    return { result: n, input: r, matches: i };
}
function markUp(e) {
    var r = e.match(/"(.+?)"/g);
    Array.isArray(r) || (r = []);
    var t = {},
        n = 1;
    for (string of r) (key = `quoted_string_${n}_`), (t[key] = string), (e = e.replace(string, key)), n++;
    for (key in ((e = (e = (e = (e = (e = (e = e.replace(/(>|::|:)(\D+?)(\()/g, "$1<span class='g'>$2</span>$3")).replace(/(::|->)/g, "<span class='r'>$1</span>")).replace(/(function)/g, "<i class='b'>$1</i>")).replace(
        /(DB)/g,
        "<span class='b'>$1</span>"
    )).replace(/(\(|\)|"|,|\[|\]|;|\{|\})/g, "<span style='color:gray'>$1</span>")).replace(/(\$[a-z]+)/g, "<span style='color:white'>$1</span>")),
    t)) {
        string = t[key];
        var i = new RegExp(key, "g");
        e = e.replace(i, `<span style='color:#FFFCB2;'>${string}</span>`);
    }
    return e;
}

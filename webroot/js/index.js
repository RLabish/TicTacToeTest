const ROWS_COUNT = 3;
const COLS_COUNT = 3;
const field = document.querySelector('.field');
let _board = '---------'

function generateCols(row, colsCount, rowId) {
    for (let i = 0; i < colsCount; i++) {
        let array = _board.split('');
        const id = rowId * 3 + i + 1;
        const col = document.createElement('div');
        col.id = `c-${id}`;
        col.dataset.id = id;
        col.className = 'cell click';
        if (array[rowId * 3 + i] != '-') {
            col.className = 'cell clicked';

        }
        let t = document.createTextNode(array[rowId * 3 + i]);
        col.appendChild(t)
        row.appendChild(col);
    }
}

function generateRows(rowsCount, colsCount) {
    for (let i = 0; i < rowsCount; i++) {
        const row = document.createElement('div');
        row.className = 'row';
        generateCols(row, colsCount, i);
        field.appendChild(row);
    }
}

function createGame() {
    clearBoard();
    _board = '---------';
    $.post("/api/v1/games/create", {})
        .done(function (data) {
            window.location.hash = '' + data.result.location
            generateRows(ROWS_COUNT, COLS_COUNT, _board);
        });
}

function clearBoard() {
    $('.field').empty();
}

function removeGame(id) {
    $.ajax({
        url: "/api/v1/games/delete/" + id,
        method: "DELETE",
        xhrFields: {
            withCredentials: true
        },
        success: function (data) {
            $('#row' + id).remove();

        }
    });
}

(function main() {
    if (window.location.hash.length > 0) {
        $.get('/api/v1/games/' + window.location.hash.replace('#', '')).done(function (data) {
            _board = data.result.game.board
            generateRows(ROWS_COUNT, COLS_COUNT);
        })
    }

    $('#start').click(function () {
        createGame()
    })


    $('.container').on('click', '.delete', function (e) {
        e.preventDefault();
        removeGame($(this).data("id"))
        return false;

    });
    $('.container').on('click', '.view', function (e) {
        e.preventDefault();
        // window.location.href = '#'+$(this).data("id")
        clearBoard();
        $.get('/api/v1/games/' + $(this).data("id")).done(function (data) {
            _board = data.result.game.board
            generateRows(ROWS_COUNT, COLS_COUNT);
        })
        return false;
    });

    var table = $("table tbody");
    $.ajax({
        url: '/api/v1/games',
        method: "GET",
        xhrFields: {
            withCredentials: true
        },
        success: function (data) {
            console.log(data);
            table.empty();
            $.each(data.result.games, function (a, b) {
                table.append("<tr id='row" + b.id + "'><td>" + b.id + "</td>" +
                    "<td>" + b.status + "</td>" +
                    "<td><button class='view' data-id='" + b.id + "'>View</button><button class='delete' data-id='" + b.id + "'>Delete</button></td></tr>");
            });

            $("#example").DataTable({
                deleteUrl: '/admin/news/news/delete/',
            });
        }
    });

    $('.field').on('click', '.click', function (e) {
        let array = _board.split('');
        array[$(this).data("id") - 1] = 'x';
        _board = array.join("")
        clearBoard();
        generateRows(ROWS_COUNT, COLS_COUNT);
        let url = "/api/v1/games/update/" + window.location.hash.replace('#', '');
        $.ajax({
            url: url,
            type: 'PUT',
            dataType: 'json',
            data: {board: _board},
            success: function (data, textStatus, xhr) {
                if (data.result.game.status == 'RUNNING') {
                    _board = data.result.game.board
                    clearBoard();
                    generateRows(ROWS_COUNT, COLS_COUNT);
                } else {
                    clearBoard();
                    alert(data.result.game.status);
                    window.location.hash = ''
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                console.log('Error in Operation');
            }
        });
    });

})()

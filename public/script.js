$(document).ready(function() {
    let timer, startTime, endTime, totalTimeWorkedTodayInSeconds = 0;
    let time = { hours: 0, minutes: 0, seconds: 0 };
    const taskSummary = {};

    function updateDisplay(id, value) {
        $(id).text(value);
    }

    function formatTime(timeObj) {
        return Object.values(timeObj).map(unit => String(unit).padStart(2, '0')).join(':');
    }

    function updateTotalTimeWorkedToday() {
        const totalTime = formatTime({
            hours: Math.floor(totalTimeWorkedTodayInSeconds / 3600),
            minutes: Math.floor((totalTimeWorkedTodayInSeconds % 3600) / 60),
            seconds: totalTimeWorkedTodayInSeconds % 60
        });
        updateDisplay('#total-time', totalTime);
    }

    function startTimer() {
        startTime = new Date();
        timer = setInterval(() => {
            time.seconds++;
            if (time.seconds >= 60) {
                time.seconds = 0;
                time.minutes++;
            }
            if (time.minutes >= 60) {
                time.minutes = 0;
                time.hours++;
            }
            updateDisplay('#time', formatTime(time));
        }, 1000);
    }

    function stopTimer() {
        clearInterval(timer);
        const taskName = $('#task_name').val();
        if (taskName) {
            taskSummary[taskName] = (taskSummary[taskName] || 0) + time.hours * 3600 + time.minutes * 60 + time.seconds;
            updateSummary();
        }
        time = { hours: 0, minutes: 0, seconds: 0 };
        updateDisplay('#time', formatTime(time));
    }

    function updateSummary() {
        const $taskList = $('#task-list').empty();
        $.each(taskSummary, (name, duration) => {
            const hrs = Math.floor(duration / 3600);
            const mins = Math.floor((duration % 3600) / 60);
            const secs = duration % 60;
            $taskList.append(`<tr><td>${name}</td><td>${hrs}h ${mins}m ${secs}s</td></tr>`);
        });
    }

    function formatDateToMySQL(date) {
        return date.toISOString().slice(0, 19).replace('T', ' ');
    }

    $('#stop').click(function() {
        $(this).add('#task_start').prop('disabled', function(i, v) { return !v; });
        stopTimer();
        endTime = new Date();
        const elapsedTimeInSeconds = Math.floor((endTime - startTime) / 1000);
        totalTimeWorkedTodayInSeconds += elapsedTimeInSeconds;
        updateTotalTimeWorkedToday();
        const taskData = {
            name: $('#task_name').val(),
            time: elapsedTimeInSeconds,
			status: 'ended',
            startTime: formatDateToMySQL(startTime),
            endTime: formatDateToMySQL(endTime)
        };
        $.post('/api/insert-task', JSON.stringify(taskData), 'json')
        .done(response => {
            if (response.status === 'success') alert('Task inserted successfully.');
        })
        .fail(() => alert('An error occurred inserting the task.'));
    });

    $('form').submit(function(e) {
        e.preventDefault();
        if ($('#task_name').val()) {
            $('#task_start').prop('disabled', true);
            $('#stop').prop('disabled', false);
            startTimer();
        }
    });
});

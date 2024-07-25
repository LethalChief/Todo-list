document.addEventListener("DOMContentLoaded", function () {
  const currentMonthElement = document.getElementById("currentMonth");
  const calendarGrid = document.getElementById("calendarGrid");
  const prevMonthBtn = document.getElementById("prevMonthBtn");
  const nextMonthBtn = document.getElementById("nextMonthBtn");

  let currentDate = new Date();
  const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;

  function updateCalendar() {
    const month = currentDate.getMonth();
    const year = currentDate.getFullYear();
    const firstDay = new Date(Date.UTC(year, month, 1)).getUTCDay();
    const daysInMonth = new Date(Date.UTC(year, month + 1, 0)).getUTCDate();

    currentMonthElement.textContent = currentDate.toLocaleDateString("en-US", {
      month: "long",
      year: "numeric",
    });

    calendarGrid.innerHTML = "";

    for (let i = 0; i < firstDay; i++) {
      const emptyDiv = document.createElement("div");
      calendarGrid.appendChild(emptyDiv);
    }

    for (let day = 1; day <= daysInMonth; day++) {
      const date = new Date(Date.UTC(year, month, day))
        .toISOString()
        .split("T")[0];
      const dayDiv = document.createElement("div");
      dayDiv.textContent = day;
      dayDiv.className = "calendar-day";
      dayDiv.setAttribute("data-date", date);

      calendarGrid.appendChild(dayDiv);
    }

    fetchEventsAndHighlight();
  }

  function fetchEventsAndHighlight() {
    const month = currentDate.getMonth();
    const year = currentDate.getFullYear();

    fetch(
      `/api/events?month=${month}&year=${year}&timezone=${encodeURIComponent(
        userTimeZone
      )}`
    )
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok " + response.statusText);
        }
        return response.json();
      })
      .then((events) => {
        events.forEach((event) => {
          const dayDiv = document.querySelector(
            `.calendar-day[data-date="${event.due_date}"]`
          );
          if (dayDiv) {
            dayDiv.classList.add("event");
            dayDiv.addEventListener("click", function () {
              displayEventsForDate(event.due_date);
            });
          }
        });
      })
      .catch((error) => {
        console.error("Fetch error:", error);
      });
  }

  function displayEventsForDate(date) {
    fetch(`/events?date=${date}&timezone=${encodeURIComponent(userTimeZone)}`)
      .then((response) => response.json())
      .then((events) => {
        const eventList = document.getElementById("event-list");
        eventList.innerHTML = "<h2>Events for " + formatDate(date) + "</h2>";
        events.forEach((event) => {
          const eventItem = document.createElement("div");
          const eventTime = new Date(event.due_date).toLocaleTimeString([], {
            hour: "2-digit",
            minute: "2-digit",
          });
          eventItem.innerHTML = `<p>${event.title} - ${eventTime}</p>`;
          eventList.appendChild(eventItem);
        });
      });
  }

  function formatDate(dateStr) {
    const date = new Date(dateStr);
    const day = String(date.getUTCDate()).padStart(2, "0");
    const month = String(date.getUTCMonth() + 1).padStart(2, "0");
    const year = date.getUTCFullYear();
    return `${month}-${day}-${year}`;
  }

  prevMonthBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    updateCalendar();
  });

  nextMonthBtn.addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    updateCalendar();
  });

  updateCalendar();
});

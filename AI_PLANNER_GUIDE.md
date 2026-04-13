# 🤖 AI-POWERED SMART TRAVEL PLANNER

## Overview

The Smart Travel Planner is an **intelligent travel package optimization engine** that uses data-driven analysis to create feasible, cost-effective itineraries. It analyzes geography, distances, and travel times to suggest optimized routes automatically.

---

## 🎯 Core Features

### **1. Intelligent Route Analysis**
- ✅ Calculates distances between all locations using Haversine formula
- ✅ Estimates travel time based on Sri Lankan road conditions (avg 45 km/h)
- ✅ Detects impractical hops (>6 hours travel in one day)
- ✅ Analyzes entire itinerary for feasibility

### **2. Smart Alternative Suggestions**
- ✅ Finds nearby alternative locations within 80 km radius
- ✅ Ranks alternatives by distance
- ✅ Suggests substitutions automatically when routes are impractical
- ✅ Helps users optimize their trip without manual research

### **3. Dynamic Cost Calculation**
- ✅ Activity costs calculated per person (adults & children rates differ)
- ✅ Transport costs based on total distance and group size
- ✅ Real-time cost updates as users modify selections
- ✅ Breakdown by activity and day

### **4. Timeline Generation**
- ✅ Builds realistic day-by-day schedules
- ✅ Includes travel time, activities, and breaks (lunch)
- ✅ Shows start/end times for all activities
- ✅ Considers activity duration from database

### **5. Complete Package Summary**
- ✅ Auto-generates custom package name from locations
- ✅ Displays all trip details in professional format
- ✅ Shows route feasibility analysis
- ✅ Provides payment schedule (50/50 split)

---

## 📊 Architecture

### **SmartTravelPlanner Class**

Main intelligent planning engine with core methods:

```php
class SmartTravelPlanner {
    // Location & activity indices for fast lookups
    private $locations_index;      // All locations with coordinates
    private $tours_index;          // All activities by location
    
    // Core Methods:
    getDistance($loc1, $loc2)           // Haversine distance calculation
    getTravelHours($loc1, $loc2)        // Travel time estimation
    getNearbyLocations($loc, $radius)   // Find alternatives
    isHopFeasible($loc1, $loc2)         // Check if hop is practical
    analyzeItinerary($dayPlanData)      // Full route analysis
    calculateActivityCost(...)          // Cost breakdown
    buildDayTimeline(...)               // Schedule generation
    generatePackageSummary(...)         // Complete package overview
}
```

### **Smart Planner API** (`smart_planner_api.php`)

RESTful endpoints for frontend integration:

```
POST /smart_planner_api.php?action=analyze_itinerary
POST /smart_planner_api.php?action=get_nearby_locations
GET  /smart_planner_api.php?action=get_location_activities
POST /smart_planner_api.php?action=calculate_costs
POST /smart_planner_api.php?action=generate_summary
POST /smart_planner_api.php?action=suggest_alternative
```

---

## 🔄 How It Works

### **Step 1: User Input**
User selects:
- Trip duration (1-14 days)
- Locations for each day
- Activities for each location
- Group composition (adults/children)

### **Step 2: Intelligent Analysis**
System analyzes:
- Distance between consecutive locations
- Travel time for each hop
- Feasibility of selected route
- Cost breakdown by activity and transport

### **Step 3: Smart Suggestions**
If a hop is impractical (>6 hours):
- System detects the issue
- Finds nearby alternative locations
- Suggests top 3 alternatives
- User can accept or override

### **Step 4: Optimization**
System calculates:
- Complete day-by-day timeline
- Activity scheduling with breaks
- Transport costs based on actual distances
- Total cost with payment schedule

### **Step 5: Presentation**
Beautiful summary shows:
- Custom package name (auto-generated)
- All trip details
- Route feasibility analysis
- Complete cost breakdown
- Realistic payment schedule

---

## 📍 Distance & Feasibility Logic

### **Haversine Formula**
```javascript
distance = 2 × R × arcsin(√(sin²(Δlat/2) + cos(lat1) × cos(lat2) × sin²(Δlng/2)))
```
- R = 6,371 km (Earth's radius)
- Accurate to ~0.5% for real-world distances
- Used for all location pair calculations

### **Travel Time Estimation**
```
Travel Hours = Distance (km) / 45 (avg speed)
```
- 45 km/h is realistic for Sri Lankan road conditions
- Accounts for traffic, road quality, terrain
- Conservative estimate ensures schedules are achievable

### **Feasibility Threshold**
```
Max Travel Hours Per Day = 6 hours (configurable)
```
- If hop > 6 hours → flagged as impractical
- Suggestions provided for alternatives
- User can override if they prefer

---

## 💰 Cost Calculation

### **Activity Costs**
```
Activity Total = (Adults × Adult Price) + (Children × Child Price)
```

### **Transport Costs**
```
Transport Cost = Total Distance (km) × $0.6 per km
```

### **Payment Schedule**
```
Total Cost = Activity Cost + Transport Cost
Pay Now = Total / 2
Pay on Arrival = Total / 2
```

---

## 🗺️ Example: Galle → Jaffna Issue

### **User Scenario**
- Day 1: Galle
- Day 2: Jaffna (impractical!)
- Day 3: Colombo

### **Distance Analysis**
```
Galle → Jaffna: 425 km
Travel Time: 425 ÷ 45 = 9.4 hours (EXCEEDS 6-HOUR LIMIT!)
```

### **Smart Solution**
System suggests alternatives for Day 2:
```
1. Matara (25 km from Galle) - nearby resort town
2. Mirissa (60 km from Galle) - whale watching
3. Balapitiya (78 km from Galle) - river safaris
```

### **User Options**
- ✅ Accept: Galle → Matara → Colombo (feasible)
- ✅ Extend: Galle → Matara → Mirissa → Colombo (4-day trip)
- ❌ Override: Galle → Jaffna anyway (user agrees to 9+ hours)

---

## 📊 API Endpoints

### **1. Analyze Itinerary**
```
POST /smart_planner_api.php?action=analyze_itinerary

Input:
{
  "day_plan_data": {...}
}

Output:
{
  "feasible": true|false,
  "issues": [...],
  "suggestions": [...],
  "total_distance_km": 456,
  "total_travel_hours": 10.5
}
```

### **2. Get Nearby Locations**
```
GET /smart_planner_api.php?action=get_nearby_locations&location=Galle

Output:
{
  "location": "Galle",
  "nearby": [
    {"location": "Matara", "distance_km": 25, "hours": 0.6},
    {"location": "Mirissa", "distance_km": 60, "hours": 1.3},
    ...
  ],
  "count": 8
}
```

### **3. Get Location Activities**
```
GET /smart_planner_api.php?action=get_location_activities&location=Galle

Output:
{
  "location": "Galle",
  "activities": [
    {
      "id": 1,
      "name": "Galle Fort Guided Tour",
      "adult_price": 85,
      "child_price": 45,
      "duration_min": 120
    },
    ...
  ],
  "count": 5
}
```

### **4. Calculate Costs**
```
POST /smart_planner_api.php?action=calculate_costs

Input:
{
  "activities": [...],
  "num_adults": 2,
  "num_children": 1,
  "total_distance_km": 456
}

Output:
{
  "activity_cost": 1250.00,
  "transport_cost": 273.60,
  "total_cost": 1523.60,
  "pay_on_arrival": 761.80
}
```

### **5. Generate Summary**
```
POST /smart_planner_api.php?action=generate_summary

Output:
{
  "locations": ["Galle", "Kandy", "Ella"],
  "package_name": "Galle & Kandy & Ella",
  "total_cost": 2500.00,
  "transport_cost": 350.00,
  "total_distance_km": 583,
  "route_analysis": {...},
  "activities_by_day": {...},
  "payment_on_arrival": 1250.00
}
```

---

## 🧠 AI Decision Logic

### **Route Feasibility Algorithm**

```
1. For each day transition:
   a. Get current location and next location
   b. Calculate distance using Haversine
   c. Calculate travel time = distance ÷ 45
   d. Check if travel_time ≤ MAX_TRAVEL_HOURS
   
2. If feasible:
   ✓ Mark as OK
   ✓ Add to timeline
   
3. If not feasible:
   ✗ Flag as issue
   ✗ Find nearby alternatives
   ✗ Suggest top 3 alternatives to user
   ✗ Wait for user decision
```

### **Alternative Finding Algorithm**

```
1. Get base location coordinates
2. For each location in database:
   a. If location = base location, skip
   b. If location = excluded location, skip
   c. Calculate distance to base
   d. If distance ≤ NEARBY_RADIUS (80 km):
      - Add to candidates list
      - Store distance and travel hours
3. Sort candidates by distance (ascending)
4. Return top 5 candidates
```

---

## 📈 Performance Optimization

### **Indexing Strategy**
- **Location Index**: O(1) lookup by name
- **Tours Index**: O(1) lookup by location
- No database queries after initial load
- All calculations in-memory

### **Caching**
- Distance calculations cached per pair
- Activity lists cached by location
- Summary generation is stateless

### **Complexity**
- Route analysis: O(n) where n = number of days
- Distance calculations: O(1) per pair
- Alternative finding: O(m) where m = total locations

---

## 🎯 Use Cases

### **Use Case 1: Optimization**
User: "I want to visit Galle, Kandy, and Ella in 4 days"
System: Detects Galle→Kandy is 6.5 hours (too far for one day)
Result: Suggests inserting Matara between Galle and Kandy

### **Use Case 2: Cost Planning**
User: "How much will this trip cost for 4 people?"
System: Calculates activities ($2,000) + transport ($300) = $2,300 total
Result: Pay Now ($1,150) + Pay on Arrival ($1,150)

### **Use Case 3: Scheduling**
User: "What time should we leave each day?"
System: Builds timeline with travel + activities + breaks
Result: Day 1: Leave 8:00 AM → Galle 9:30 AM → Activities → Return 5:00 PM

### **Use Case 4: Comparison**
User: "What if I extend to 5 days?"
System: Recalculates all distances, costs, and timelines
Result: Shows new route, costs, and improvement in feasibility

---

## 🔌 Integration with cust_new.php

The Smart Planner is called from the booking flow:

```
cust_new.php
    ↓
User selects locations & activities
    ↓
smart_planner_api.php (analyze_itinerary)
    ↓
Check if route is feasible
    ↓
If issues found:
  - Show warnings/suggestions
  - User can modify selections
    ↓
booking_summary.php
    ↓
smart_planner_api.php (generate_summary)
    ↓
Display optimized package
```

---

## 📚 Database Requirements

### **locations table**
```sql
CREATE TABLE locations (
    id INT PRIMARY KEY,
    name VARCHAR(100),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    region VARCHAR(50)
);
```

### **custom_tours table**
```sql
CREATE TABLE custom_tours (
    id INT PRIMARY KEY,
    activity VARCHAR(200),
    location VARCHAR(100),
    foreign_adult_usd DECIMAL(10,2),
    foreign_child_usd DECIMAL(10,2),
    duration_minutes INT
);
```

---

## ⚙️ Configuration

Adjustable parameters in SmartTravelPlanner.php:

```php
const AVG_SPEED_KMH = 45;              // Average speed
const MAX_TRAVEL_HOURS_PER_DAY = 6;    // Max driving per day
const NEARBY_RADIUS_KM = 80;           // Alternative search radius
const TRANSPORT_COST_PER_KM = 0.6;     // Cost per km
```

---

## 🧪 Testing Examples

### **Test 1: Feasibility Check**
```php
$planner = new SmartTravelPlanner($conn);
$hours = $planner->getTravelHours('Galle', 'Jaffna');
echo $hours; // Output: 9.4
```

### **Test 2: Find Alternatives**
```php
$nearby = $planner->getNearbyLocations('Galle', 80);
// Returns: Matara, Mirissa, Balapitiya, etc.
```

### **Test 3: Analyze Itinerary**
```php
$analysis = $planner->analyzeItinerary($dayPlanData);
echo $analysis['feasible']; // true or false
echo $analysis['total_distance_km']; // 456
```

---

## 📝 Summary

**Smart Travel Planner delivers:**
- ✅ Intelligent distance-based optimization
- ✅ Automatic feasibility checking
- ✅ Smart alternative suggestions
- ✅ Real-time cost calculations
- ✅ Professional package summaries
- ✅ User-friendly route management

**Result:** Users can book trips with confidence that routes are practical, costs are accurate, and schedules are achievable!

---

**Version:** 1.0 AI-Powered Travel Planner  
**Status:** ✅ **READY FOR DEPLOYMENT**  
**Last Updated:** December 7, 2025
